<?php


namespace AppBundle\Command;


use AppBundle\Controller\MobiusWebhookController;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Queries the user's balance for this DApp
 */
class MobiusDAppBalanceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('mobius:app-store:balance')
            ->setDescription("Queries a user's balance for this app")
            ->addArgument('email', InputArgument::REQUIRED, 'DApp store email address of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $appStore = $this->getContainer()->get('mobius.app_store');

        $output->writeln(sprintf('User   : <comment>%s</comment>', $email));
        $output->writeln(sprintf('Balance: <comment>%s</comment>', $appStore->getBalance($email)));
    }
}