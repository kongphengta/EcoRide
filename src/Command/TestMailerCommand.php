<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'app:test-mailer', description: 'Envoie un email de test avec Symfony Mailer.')]
class TestMailerCommand extends Command
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setDescription('Envoie un email de test avec Symfony Mailer.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('kong.vixay@gmail.com')
            ->to('kong.vixay@gmail.com')
            ->subject('Test Symfony Mailer')
            ->text('Ceci est un test d\'envoi d\'email via le mot de passe d\'application Gmail.');

        try {
            $this->mailer->send($email);
            $output->writeln('<info>Email envoyé avec succès !</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Erreur lors de l\'envoi : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
