<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210415225244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE realty ADD id_agency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT FK_627221C4DDF670D FOREIGN KEY (id_agency_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_627221C4DDF670D ON realty (id_agency_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT FK_627221C4DDF670D');
        $this->addSql('DROP INDEX IDX_627221C4DDF670D');
        $this->addSql('ALTER TABLE realty DROP id_agency_id');
    }
}
