<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414152907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, commentaire LONGTEXT NOT NULL, note INT NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE avis_user (avis_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_42223E48197E709F (avis_id), INDEX IDX_42223E48A76ED395 (user_id), PRIMARY KEY(avis_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_A5E2A5D7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE parametre (id INT AUTO_INCREMENT NOT NULL, configuration_id INT NOT NULL, propriete VARCHAR(255) NOT NULL, valeur VARCHAR(255) NOT NULL, INDEX IDX_ACC7904173F32DD8 (configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_avis (user_id INT NOT NULL, avis_id INT NOT NULL, INDEX IDX_F510E739A76ED395 (user_id), INDEX IDX_F510E739197E709F (avis_id), PRIMARY KEY(user_id, avis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user ADD CONSTRAINT FK_42223E48197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user ADD CONSTRAINT FK_42223E48A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE configuration ADD CONSTRAINT FK_A5E2A5D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE parametre ADD CONSTRAINT FK_ACC7904173F32DD8 FOREIGN KEY (configuration_id) REFERENCES configuration (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture ADD marque_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E9E2810F4827B9B2 ON voiture (marque_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F4827B9B2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user DROP FOREIGN KEY FK_42223E48197E709F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user DROP FOREIGN KEY FK_42223E48A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE configuration DROP FOREIGN KEY FK_A5E2A5D7A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE parametre DROP FOREIGN KEY FK_ACC7904173F32DD8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739197E709F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avis
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avis_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE configuration
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE marque
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE parametre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_avis
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E9E2810F4827B9B2 ON voiture
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture DROP marque_id
        SQL);
    }
}
