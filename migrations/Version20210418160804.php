<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210418160804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
        CREATE TABLE document (id INT NOT NULL, id_realty_id INT NOT NULL, url VARCHAR(255) NOT NULL, ask_remove BOOLEAN DEFAULT "false" NOT NULL, PRIMARY KEY(id));
        CREATE INDEX IDX_D8698A76F1F0D04E ON document (id_realty_id);
        CREATE TABLE image (id INT NOT NULL, id_realty_id INT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id));
        CREATE INDEX IDX_C53D045FF1F0D04E ON image (id_realty_id);
        CREATE TABLE message (id INT NOT NULL, id_sender_id INT NOT NULL, id_receiver_id INT DEFAULT NULL, id_owner_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type INT NOT NULL, PRIMARY KEY(id));
        CREATE INDEX IDX_B6BD307F76110FBA ON message (id_sender_id);
        CREATE INDEX IDX_B6BD307FD5412041 ON message (id_receiver_id);
        CREATE INDEX IDX_B6BD307F2EE78D6C ON message (id_owner_id);
        CREATE TABLE realty (id INT NOT NULL, id_owner_id INT NOT NULL, id_tenant_id INT DEFAULT NULL, id_agency_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, rent INT NOT NULL, statut INT NOT NULL, PRIMARY KEY(id));
        CREATE INDEX IDX_627221C2EE78D6C ON realty (id_owner_id);
        CREATE UNIQUE INDEX UNIQ_627221C10069D0D ON realty (id_tenant_id);
        CREATE INDEX IDX_627221C4DDF670D ON realty (id_agency_id);
        CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(254) NOT NULL, is_active BOOLEAN NOT NULL, ask_remove BOOLEAN DEFAULT "false" NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, register TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, subscribe TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id));
        CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username);
        CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email);
        CREATE SEQUENCE document_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
        CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
        CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
        CREATE SEQUENCE realty_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
        CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1;
        ALTER TABLE document ADD CONSTRAINT FK_D8698A76F1F0D04E FOREIGN KEY (id_realty_id) REFERENCES realty (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ALTER TABLE image ADD CONSTRAINT FK_C53D045FF1F0D04E FOREIGN KEY (id_realty_id) REFERENCES realty (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ALTER TABLE message ADD CONSTRAINT FK_B6BD307F76110FBA FOREIGN KEY (id_sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD5412041 FOREIGN KEY (id_receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2EE78D6C FOREIGN KEY (id_owner_id) REFERENCES realty (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ALTER TABLE realty ADD CONSTRAINT FK_627221C2EE78D6C FOREIGN KEY (id_owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ALTER TABLE realty ADD CONSTRAINT FK_627221C10069D0D FOREIGN KEY (id_tenant_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;
        ALTER TABLE realty ADD CONSTRAINT FK_627221C4DDF670D FOREIGN KEY (id_agency_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
