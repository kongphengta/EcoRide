<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MarqueFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $marques = [
            'Renault',
            'Peugeot',
            'Citroën',
            'Volkswagen',
            'Ford',
            'Opel',
            'Fiat',
            'Audi',
            'Kia',
            'Hyundai',
            'Skoda',
            'Chevrolet',
            'Subaru',
            'Mazda',
            'Dacia',
            'Volvo',
            'Land Rover',
            'Porsche',
            'Lexus',
            'Jaguar',
            'Tesla',
            'Mitsubishi',
            'Chrysler',
            'Jeep',
            'Buick',
            'Toyota',
            'Nissan',
            'Honda',
            'BMW',
            'Mercedes-Benz'
        ];
        foreach ($marques as $libelle) {
            $marque = new Marque();
            $marque->setLibelle($libelle);
            $manager->persist($marque);
        }

        $manager->flush();
    }
}
