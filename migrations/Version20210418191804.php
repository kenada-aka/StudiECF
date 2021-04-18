<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210418191804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE document_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE realty_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE document (id INT NOT NULL, id_realty_id INT NOT NULL, url VARCHAR(255) NOT NULL, ask_remove BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D8698A76F1F0D04E ON document (id_realty_id)');
        $this->addSql('CREATE TABLE image (id INT NOT NULL, id_realty_id INT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C53D045FF1F0D04E ON image (id_realty_id)');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, id_sender_id INT NOT NULL, id_receiver_id INT DEFAULT NULL, id_owner_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F76110FBA ON message (id_sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FD5412041 ON message (id_receiver_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F2EE78D6C ON message (id_owner_id)');
        $this->addSql('CREATE TABLE realty (id INT NOT NULL, id_owner_id INT NOT NULL, id_tenant_id INT DEFAULT NULL, id_agency_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, rent INT NOT NULL, statut INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_627221C2EE78D6C ON realty (id_owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_627221C10069D0D ON realty (id_tenant_id)');
        $this->addSql('CREATE INDEX IDX_627221C4DDF670D ON realty (id_agency_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(254) NOT NULL, is_active BOOLEAN NOT NULL, ask_remove BOOLEAN DEFAULT \'false\' NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, register TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, subscribe TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76F1F0D04E FOREIGN KEY (id_realty_id) REFERENCES realty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FF1F0D04E FOREIGN KEY (id_realty_id) REFERENCES realty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F76110FBA FOREIGN KEY (id_sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD5412041 FOREIGN KEY (id_receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2EE78D6C FOREIGN KEY (id_owner_id) REFERENCES realty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT FK_627221C2EE78D6C FOREIGN KEY (id_owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT FK_627221C10069D0D FOREIGN KEY (id_tenant_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE realty ADD CONSTRAINT FK_627221C4DDF670D FOREIGN KEY (id_agency_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A76F1F0D04E');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT FK_C53D045FF1F0D04E');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F2EE78D6C');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F76110FBA');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FD5412041');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT FK_627221C2EE78D6C');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT FK_627221C10069D0D');
        $this->addSql('ALTER TABLE realty DROP CONSTRAINT FK_627221C4DDF670D');
        $this->addSql('DROP SEQUENCE document_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE realty_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE realty');
        $this->addSql('DROP TABLE "user"');
    }
}
