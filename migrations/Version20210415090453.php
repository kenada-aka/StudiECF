<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210415090453 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE realty ADD statut INT');
        $this->addSql('ALTER TABLE realty DROP to_rent');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE realty ADD to_rent BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE realty DROP statut');
        $this->addSql('ALTER TABLE "user" ALTER firstname DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER register DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER subscribe DROP NOT NULL');
    }
}
