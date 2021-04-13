<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413094834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE realty ADD id_owner_id INT NULL');
        $this->addSql('ALTER TABLE realty ADD id_tenant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT FK_627221C2EE78D6C FOREIGN KEY (id_owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT FK_627221C10069D0D FOREIGN KEY (id_tenant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_627221C2EE78D6C ON realty (id_owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_627221C10069D0D ON realty (id_tenant_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT FK_627221C2EE78D6C');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT FK_627221C10069D0D');
        $this->addSql('DROP INDEX UNIQ_627221C2EE78D6C');
        $this->addSql('DROP INDEX UNIQ_627221C10069D0D');
        $this->addSql('ALTER TABLE realty DROP id_owner_id');
        $this->addSql('ALTER TABLE realty DROP id_tenant_id');
    }
}
