<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131200822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, conducteur_id INT NOT NULL, ville_depart VARCHAR(255) NOT NULL, ville_arrivee VARCHAR(255) NOT NULL, date_depart DATETIME NOT NULL, nombre_places INT NOT NULL, prix DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, heure_arrivee_estimee TIME NOT NULL, itineraire_id INT NOT NULL, date_publication DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, voiture_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE trajet');
    }
}
