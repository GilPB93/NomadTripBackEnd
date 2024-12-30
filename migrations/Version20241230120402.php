<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230120402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travelbook ADD user_travelbooks_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE travelbook ADD CONSTRAINT FK_BFDE9B7BA3562515 FOREIGN KEY (user_travelbooks_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BFDE9B7BA3562515 ON travelbook (user_travelbooks_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travelbook DROP FOREIGN KEY FK_BFDE9B7BA3562515');
        $this->addSql('DROP INDEX IDX_BFDE9B7BA3562515 ON travelbook');
        $this->addSql('ALTER TABLE travelbook DROP user_travelbooks_id');
    }
}
