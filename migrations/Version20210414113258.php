<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414113258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_8d93d6497ba2f5eb');
        $this->addSql('ALTER TABLE "user" DROP api_token');
        $this->addSql('ALTER TABLE "user" ALTER roles SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER username SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER email SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER is_active SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD api_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ALTER username DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER email DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER is_active DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER roles DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d6497ba2f5eb ON "user" (api_token)');
    }
}
