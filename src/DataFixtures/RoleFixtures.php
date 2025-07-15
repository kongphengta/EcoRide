<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public const ROLES = [
        'ROLE_CHAUFFEUR',
        'ROLE_ADMIN',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::ROLES as $roleLibelle) {
            // On vérifie si le rôle existe déjà pour éviter les doublons
            $existingRole = $manager->getRepository(Role::class)->findOneBy(['libelle' => $roleLibelle]);

            if (!$existingRole) {
                $role = new Role();
                $role->setLibelle($roleLibelle);
                $manager->persist($role);
            }
        }

        $manager->flush();
    }
}
