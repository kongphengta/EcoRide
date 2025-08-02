<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728101510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD reservation_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F91ABF0B83297E7 ON avis (reservation_id)');
        $this->addSql('ALTER TABLE user ADD credits INT NOT NULL');
        $this->addSql('ALTER TABLE voiture CHANGE energie motorisation VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0B83297E7');
        $this->addSql('DROP INDEX UNIQ_8F91ABF0B83297E7 ON avis');
        $this->addSql('ALTER TABLE avis DROP reservation_id');
        $this->addSql('ALTER TABLE `user` DROP credits');
        $this->addSql('ALTER TABLE voiture CHANGE motorisation energie VARCHAR(50) NOT NULL');
    }
}
