<?php


namespace AppBundle\Controller;


use AppBundle\Entity\MobiusUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/coin-flip")
 */
class CoinFlipController extends Controller
{
    /**
     * @Route("/", name="coinFlipIndex")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        $mobiusUser = $this->getMobiusUserByEmail($request->get('email'));
        if (!$mobiusUser) {
            throw new \InvalidArgumentException('User not found. Try funding a user with: bin/console mobius:simulator:app-store-deposit user@example.com 100');
        }

        return $this->render('coin-flip/index.html.twig', [
            'mobiusUser' => $mobiusUser
        ]);
    }

    /**
     * @Route("/flip", name="coinFlipDo")
     * @Method({"POST"})
     */
    public function doFlipAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $email = $request->request->get('email');
        $guess = $request->request->get('guess');

        $mobiusUser = $this->getMobiusUserByEmail($request->request->get('email'));
        if (!$mobiusUser) {
            throw new \InvalidArgumentException('Unknown user, please add credits for this application in the DApp store at https://mobius.network/store');
        }

        // User must have at least one MOBI
        if ($mobiusUser->getBalance() < 1) {
            $this->addFlash('error', 'You must have at least 1 MOBI to play');
            return $this->redirectToRoute('coinFlipIndex', ['email' => $email]);
        }

        // todo: replace with provably-fair blockchain implementation
        $result = rand(0, 1);

        // 1 = tails, 0 = heads
        $resultStr = ($result) ? 'Tails' : 'Heads';

        if ($guess == $result) {
            sleep(1);
            $this->addFlash('success', sprintf('%s! You get to keep your MOBI!', $resultStr));
        }
        else {
            // Spend their MOBI
            $newBalance = $this->get('mobius.app_store')
                ->useBalance($email, 1);

            // todo: in a real implementation this would use locking to prevent double spends
            $mobiusUser->setBalance($newBalance);
            $em->persist($mobiusUser);
            $em->flush($mobiusUser);

            $this->addFlash('error', sprintf('%s! Sorry, you lost 1 MOBI :(', $resultStr));
        }

        return $this->redirectToRoute('coinFlipIndex', ['email' => $email]);
    }

    /**
     * @param $email
     * @return MobiusUser|null|object
     */
    protected function getMobiusUserByEmail($email)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        // See if they're in the local database
        $mobiusUser = $em->getRepository('AppBundle:MobiusUser')
            ->findOneBy(['mobiusEmail' => $email]);

        if (!$mobiusUser) {
            // If not, they may be a user we haven't seen yet but they've made a
            // deposit without hitting the webhook
            $appStoreBalance = $this->get('mobius.app_store')
                ->getBalance($email);

            // If so, create a new user for them in this DApp
            if ($appStoreBalance) {
                $mobiusUser = new MobiusUser($email);
                $mobiusUser->setBalance($appStoreBalance);
                $em->persist($mobiusUser);
                $em->flush($mobiusUser);
            }
        }

        return $mobiusUser;
    }
}