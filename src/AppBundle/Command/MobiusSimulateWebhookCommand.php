<?php


namespace AppBundle\Command;


use AppBundle\Controller\MobiusWebhookController;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Simulates a user depositing credits in the DApp store.
 */
class MobiusSimulateWebhookCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('mobius:simulator:app-store-deposit')
            ->setDescription("Simulates a user making a deposit into this DApp")
            ->addArgument('email', InputArgument::REQUIRED, 'DApp store email address of the user')
            ->addArgument('totalAmount', InputArgument::REQUIRED, 'Total amount of tokens the user should have after this deposit')
            ->addArgument('depositAmount', InputArgument::OPTIONAL, 'Amount of tokens the user is depositing in this webhook call')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $totalAmount = $input->getArgument('totalAmount');
        $depositAmount = $totalAmount;

        if ($input->getArgument('depositAmount')) {
            $totalAmount = $input->getArgument('depositAmount');
        }

        // Build the request that the DApp store would send
        $request = new Request([],
            // POST variables
            [
                'action_type' => 'app_store/deposit',
                'app_uid' => $this->getContainer()->getParameter('mobius_app_uid'),
                'email' => $email,
                'num_credits' => $depositAmount,
                'total_num_credits' => $totalAmount,
            ]
        );
        $request->headers->set('Mobius-API-Key', $this->getContainer()->getParameter('mobius_api_key'));

        // Call the controller method
        $controller = new MobiusWebhookController();
        $controller->setContainer($this->getContainer());
        $controller->appStoreDepositAction($request);

        $output->writeln(sprintf('Simulated webhook: <comment>%s</comment> now has <comment>%s</comment> credits.',
            $email,
            $totalAmount
        ));

        return 0;
    }
}