<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241223104257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` ADD role_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D7688987678 FOREIGN KEY (role_id_id) REFERENCES role (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D7688987678 ON `admin` (role_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D7688987678');
        $this->addSql('DROP INDEX UNIQ_880E0D7688987678 ON `admin`');
        $this->addSql('ALTER TABLE `admin` DROP role_id_id');
    }
}
