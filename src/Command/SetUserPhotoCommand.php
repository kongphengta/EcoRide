<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:set-photo',
    description: 'Met à jour la photo d\'un utilisateur'
)]
class SetUserPhotoCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('photo', InputArgument::REQUIRED, 'Nom du fichier photo (ex: g_sankukai.png)')
            ->setHelp('Cette commande permet de définir la photo d\'un utilisateur en base de données.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $photoName = $input->getArgument('photo');

        // Rechercher l'utilisateur
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error(sprintf('Aucun utilisateur trouvé avec l\'email: %s', $email));
            return Command::FAILURE;
        }

        // Mettre à jour la photo
        $user->setPhoto($photoName);
        $this->entityManager->flush();

        $io->success(sprintf(
            'Photo mise à jour avec succès pour l\'utilisateur %s (%s). Photo: %s',
            $user->getPseudo(),
            $user->getEmail(),
            $photoName
        ));

        return Command::SUCCESS;
    }
}
