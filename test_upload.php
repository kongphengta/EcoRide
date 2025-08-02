<?php

/**
 * Script de test pour vérifier l'upload et la sauvegarde de photos en base
 */

require_once 'vendor/autoload.php';

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

// Initialisation du kernel Symfony
$kernel = new Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

// Récupération des services
$entityManager = $container->get('doctrine.orm.entity_manager');
$userRepository = $entityManager->getRepository(App\Entity\User::class);

// Récupération de l'utilisateur de test
$user = $userRepository->findOneBy(['email' => 'f.sankukai@test.fr']);

if (!$user) {
    echo "Utilisateur de test non trouvé!\n";
    exit(1);
}

echo "Utilisateur trouvé: " . $user->getEmail() . " (ID: " . $user->getId() . ")\n";
echo "Photo actuelle: " . ($user->getPhoto() ?: 'NULL') . "\n";

// Test: définir une photo et sauvegarder
$testPhotoName = 'test_photo_' . time() . '.jpg';
echo "Test: définition de la photo '$testPhotoName'\n";

$user->setPhoto($testPhotoName);
echo "Photo définie sur l'objet: " . $user->getPhoto() . "\n";

// Flush en base
echo "Flush en base...\n";
$entityManager->flush();

// Vérification immédiate
$user2 = $userRepository->findOneBy(['email' => 'f.sankukai@test.fr']);
echo "Photo après flush (objet rechargé): " . ($user2->getPhoto() ?: 'NULL') . "\n";

// Vérification directe en SQL
$conn = $entityManager->getConnection();
$stmt = $conn->prepare('SELECT photo FROM user WHERE id = ?');
$result = $stmt->executeQuery([$user->getId()]);
$photoFromDb = $result->fetchOne();
echo "Photo directement depuis SQL: " . ($photoFromDb ?: 'NULL') . "\n";

echo "Test terminé.\n";
