<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422165317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL, ADD reset_token_created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE telephone telephone VARCHAR(50) NOT NULL, CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE date_naissance date_naissance DATE NOT NULL, CHANGE photo photo VARCHAR(255) NOT NULL, CHANGE date_inscription date_inscription DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP reset_token, DROP reset_token_created_at, CHANGE telephone telephone VARCHAR(255) DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE date_naissance date_naissance DATE DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE date_inscription date_inscription DATETIME DEFAULT NULL
        SQL);
    }
}
