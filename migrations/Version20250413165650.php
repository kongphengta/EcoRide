<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413165650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage ADD chauffeur_id INT DEFAULT NULL, ADD voiture_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E8985C0B3BE FOREIGN KEY (chauffeur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_28C79E8985C0B3BE ON covoiturage (chauffeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_28C79E89181A8BA ON covoiturage (voiture_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E8985C0B3BE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89181A8BA
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_28C79E8985C0B3BE ON covoiturage
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_28C79E89181A8BA ON covoiturage
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage DROP chauffeur_id, DROP voiture_id
        SQL);
    }
}
