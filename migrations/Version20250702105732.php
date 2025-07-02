<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250702105732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user DROP FOREIGN KEY FK_42223E48197E709F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user DROP FOREIGN KEY FK_42223E48A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739197E709F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avis_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_avis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD auteur_id INT NOT NULL, ADD receveur_id INT NOT NULL, ADD date_creation DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', DROP statut
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF060BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0B967E626 FOREIGN KEY (receveur_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8F91ABF060BB6FE6 ON avis (auteur_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8F91ABF0B967E626 ON avis (receveur_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE avis_user (avis_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_42223E48197E709F (avis_id), INDEX IDX_42223E48A76ED395 (user_id), PRIMARY KEY(avis_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_avis (user_id INT NOT NULL, avis_id INT NOT NULL, INDEX IDX_F510E739197E709F (avis_id), INDEX IDX_F510E739A76ED395 (user_id), PRIMARY KEY(user_id, avis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user ADD CONSTRAINT FK_42223E48197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis_user ADD CONSTRAINT FK_42223E48A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF060BB6FE6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0B967E626
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8F91ABF060BB6FE6 ON avis
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8F91ABF0B967E626 ON avis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD statut VARCHAR(255) NOT NULL, DROP auteur_id, DROP receveur_id, DROP date_creation
        SQL);
    }
}
