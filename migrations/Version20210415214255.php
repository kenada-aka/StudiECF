<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210415214255 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD id_owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2EE78D6C FOREIGN KEY (id_owner_id) REFERENCES realty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307F2EE78D6C ON message (id_owner_id)');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT FK_627221C10069D0D');
        $this->addSql('ALTER TABLE realty ALTER statut SET NOT NULL');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT FK_627221C10069D0D FOREIGN KEY (id_tenant_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ALTER firstname SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER register SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER subscribe SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT fk_627221c10069d0d');
        $this->addSql('ALTER TABLE realty ALTER statut DROP NOT NULL');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT fk_627221c10069d0d FOREIGN KEY (id_tenant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ALTER firstname DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER register DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER subscribe DROP NOT NULL');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F2EE78D6C');
        $this->addSql('DROP INDEX IDX_B6BD307F2EE78D6C');
        $this->addSql('ALTER TABLE message DROP id_owner_id');
    }
}
