<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415144038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE avis_user (avis_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_42223E48197E709F (avis_id), INDEX IDX_42223E48A76ED395 (user_id), PRIMARY KEY(avis_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user DROP FOREIGN KEY FK_42223E48197E709F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user DROP FOREIGN KEY FK_42223E48A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avis_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE configuration DROP FOREIGN KEY FK_A5E2A5D7A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE parametre DROP FOREIGN KEY FK_ACC7904173F32DD8
        SQL);
    }
}
