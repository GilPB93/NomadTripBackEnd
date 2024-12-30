<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230115750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_messages ADD user_contact_messages_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_messages ADD CONSTRAINT FK_41278201E1FF8F96 FOREIGN KEY (user_contact_messages_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_41278201E1FF8F96 ON contact_messages (user_contact_messages_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_messages DROP FOREIGN KEY FK_41278201E1FF8F96');
        $this->addSql('DROP INDEX IDX_41278201E1FF8F96 ON contact_messages');
        $this->addSql('ALTER TABLE contact_messages DROP user_contact_messages_id');
    }
}
