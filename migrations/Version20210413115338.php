<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413115338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_627221c2ee78d6c');
        $this->addSql('CREATE INDEX IDX_627221C2EE78D6C ON realty (id_owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_627221C2EE78D6C');
        $this->addSql('ALTER TABLE realty ALTER id_owner_id DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_627221c2ee78d6c ON realty (id_owner_id)');
    }
}
