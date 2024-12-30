<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230115923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log ADD user_activity_log_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647AACDD109 FOREIGN KEY (user_activity_log_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FD06F647AACDD109 ON activity_log (user_activity_log_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log DROP FOREIGN KEY FK_FD06F647AACDD109');
        $this->addSql('DROP INDEX IDX_FD06F647AACDD109 ON activity_log');
        $this->addSql('ALTER TABLE activity_log DROP user_activity_log_id');
    }
}
