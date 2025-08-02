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
    name: 'app:test-photo-save',
    description: 'Test la sauvegarde de photo en base pour un utilisateur'
)]
class TestPhotoSaveCommand extends Command
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
            ->addArgument('photo', InputArgument::OPTIONAL, 'Nom de la photo (par défaut: test_photo_timestamp.jpg)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $photo = $input->getArgument('photo') ?: 'test_photo_' . time() . '.jpg';

        // Récupération de l'utilisateur
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error("Utilisateur avec email '$email' non trouvé!");
            return Command::FAILURE;
        }

        $io->info("Utilisateur trouvé: {$user->getEmail()} (ID: {$user->getId()})");
        $io->info("Photo actuelle: " . ($user->getPhoto() ?: 'NULL'));

        // Test de sauvegarde
        $io->info("Test: définition de la photo '$photo'");
        $user->setPhoto($photo);
        $io->info("Photo définie sur l'objet: " . $user->getPhoto());

        // Flush en base
        $io->info("Flush en base...");
        $this->entityManager->flush();

        // Vérification immédiate (rechargement)
        $this->entityManager->refresh($user);
        $io->info("Photo après flush (objet rechargé): " . ($user->getPhoto() ?: 'NULL'));

        // Vérification directe en SQL
        $conn = $this->entityManager->getConnection();
        $stmt = $conn->prepare('SELECT photo FROM user WHERE id = ?');
        $result = $stmt->executeQuery([$user->getId()]);
        $photoFromDb = $result->fetchOne();
        $io->info("Photo directement depuis SQL: " . ($photoFromDb ?: 'NULL'));

        if ($photoFromDb === $photo) {
            $io->success("✅ Test réussi! La photo a été correctement sauvegardée en base.");
        } else {
            $io->error("❌ Test échoué! Problème de sauvegarde en base.");
            $io->warning("Attendu: '$photo', Reçu: '$photoFromDb'");
        }

        return Command::SUCCESS;
    }
}
