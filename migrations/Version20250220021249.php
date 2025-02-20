<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220021249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE configuration_parametre (configuration_id INT NOT NULL, parametre_id INT NOT NULL, INDEX IDX_26C3BC1A73F32DD8 (configuration_id), INDEX IDX_26C3BC1A6358FF62 (parametre_id), PRIMARY KEY(configuration_id, parametre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_avis (user_id INT NOT NULL, avis_id INT NOT NULL, INDEX IDX_F510E739A76ED395 (user_id), INDEX IDX_F510E739197E709F (avis_id), PRIMARY KEY(user_id, avis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_covoiturage (user_id INT NOT NULL, covoiturage_id INT NOT NULL, INDEX IDX_81DC571CA76ED395 (user_id), INDEX IDX_81DC571C62671590 (covoiturage_id), PRIMARY KEY(user_id, covoiturage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_configuration (user_id INT NOT NULL, configuration_id INT NOT NULL, INDEX IDX_4B6C0887A76ED395 (user_id), INDEX IDX_4B6C088773F32DD8 (configuration_id), PRIMARY KEY(user_id, configuration_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voiture_covoiturage (voiture_id INT NOT NULL, covoiturage_id INT NOT NULL, INDEX IDX_1703B3A5181A8BA (voiture_id), INDEX IDX_1703B3A562671590 (covoiturage_id), PRIMARY KEY(voiture_id, covoiturage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE configuration_parametre ADD CONSTRAINT FK_26C3BC1A73F32DD8 FOREIGN KEY (configuration_id) REFERENCES configuration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE configuration_parametre ADD CONSTRAINT FK_26C3BC1A6358FF62 FOREIGN KEY (parametre_id) REFERENCES parametre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_covoiturage ADD CONSTRAINT FK_81DC571CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_covoiturage ADD CONSTRAINT FK_81DC571C62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_configuration ADD CONSTRAINT FK_4B6C0887A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_configuration ADD CONSTRAINT FK_4B6C088773F32DD8 FOREIGN KEY (configuration_id) REFERENCES configuration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE voiture_covoiturage ADD CONSTRAINT FK_1703B3A5181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE voiture_covoiturage ADD CONSTRAINT FK_1703B3A562671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_57698A6AA76ED395 ON role (user_id)');
        $this->addSql('ALTER TABLE voiture ADD user_id INT DEFAULT NULL, ADD marque_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('CREATE INDEX IDX_E9E2810FA76ED395 ON voiture (user_id)');
        $this->addSql('CREATE INDEX IDX_E9E2810F4827B9B2 ON voiture (marque_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration_parametre DROP FOREIGN KEY FK_26C3BC1A73F32DD8');
        $this->addSql('ALTER TABLE configuration_parametre DROP FOREIGN KEY FK_26C3BC1A6358FF62');
        $this->addSql('ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739A76ED395');
        $this->addSql('ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739197E709F');
        $this->addSql('ALTER TABLE user_covoiturage DROP FOREIGN KEY FK_81DC571CA76ED395');
        $this->addSql('ALTER TABLE user_covoiturage DROP FOREIGN KEY FK_81DC571C62671590');
        $this->addSql('ALTER TABLE user_configuration DROP FOREIGN KEY FK_4B6C0887A76ED395');
        $this->addSql('ALTER TABLE user_configuration DROP FOREIGN KEY FK_4B6C088773F32DD8');
        $this->addSql('ALTER TABLE voiture_covoiturage DROP FOREIGN KEY FK_1703B3A5181A8BA');
        $this->addSql('ALTER TABLE voiture_covoiturage DROP FOREIGN KEY FK_1703B3A562671590');
        $this->addSql('DROP TABLE configuration_parametre');
        $this->addSql('DROP TABLE user_avis');
        $this->addSql('DROP TABLE user_covoiturage');
        $this->addSql('DROP TABLE user_configuration');
        $this->addSql('DROP TABLE voiture_covoiturage');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6AA76ED395');
        $this->addSql('DROP INDEX IDX_57698A6AA76ED395 ON role');
        $this->addSql('ALTER TABLE role DROP user_id');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FA76ED395');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F4827B9B2');
        $this->addSql('DROP INDEX IDX_E9E2810FA76ED395 ON voiture');
        $this->addSql('DROP INDEX IDX_E9E2810F4827B9B2 ON voiture');
        $this->addSql('ALTER TABLE voiture DROP user_id, DROP marque_id');
    }
}
