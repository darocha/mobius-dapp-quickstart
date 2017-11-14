<?php


namespace AppBundle\Controller;


use AppBundle\Entity\MobiusUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/mobius/webhook")
 */
class MobiusWebhookController extends Controller
{
    /**
     * @Route("/app-store-deposit")
     * @Method({"POST"})
     * @param Request $request
     */
    public function appStoreDepositAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $this->validateSource($request);

        $postVars = $request->request;

        $mobiusUser = $em->getRepository('AppBundle:MobiusUser')
            ->findOneBy(['mobiusEmail' => $postVars->get('email')]);

        if (!$mobiusUser) {
            $mobiusUser = new MobiusUser($postVars->get('email'));
        }

        // Update the user's balance to the total number of credits they have
        // in the DApp store
        $mobiusUser->setBalance($postVars->get('total_num_credits'));

        $em->persist($mobiusUser);
        $em->flush($mobiusUser);

        // Return empty successful response
        return new Response();
    }

    /**
     * Ensures that this webhook request came from Mobius
     *
     * @param Request $request
     * @throws \InvalidArgumentException
     */
    protected function validateSource(Request $request)
    {
        $postVars = $request->request;
        // Validate action_type
        if ('app_store/deposit' != $postVars->get('action_type')) throw new \InvalidArgumentException("Invalid action_type");

        // App UID must be correct
        if ($postVars->get('app_uid') != $this->getParameter('mobius_app_uid')) {
            throw new \InvalidArgumentException("Invalid app UID");
        }

        // Header must match API key
        if ($request->headers->get('Mobius-API-Key') != $this->getParameter('mobius_api_key')) {
            throw new \InvalidArgumentException("Incorrect API key");
        }
    }
}