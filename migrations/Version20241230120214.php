<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230120214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travelbook ADD list_places_id INT DEFAULT NULL, ADD list_fb_id INT DEFAULT NULL, ADD list_photos_id INT DEFAULT NULL, ADD list_souvenirs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE travelbook ADD CONSTRAINT FK_BFDE9B7BCEDBBF2C FOREIGN KEY (list_places_id) REFERENCES list_places (id)');
        $this->addSql('ALTER TABLE travelbook ADD CONSTRAINT FK_BFDE9B7B1ECC259 FOREIGN KEY (list_fb_id) REFERENCES list_fb (id)');
        $this->addSql('ALTER TABLE travelbook ADD CONSTRAINT FK_BFDE9B7B4ECDE009 FOREIGN KEY (list_photos_id) REFERENCES list_photos (id)');
        $this->addSql('ALTER TABLE travelbook ADD CONSTRAINT FK_BFDE9B7BDF08107 FOREIGN KEY (list_souvenirs_id) REFERENCES list_souvenirs (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFDE9B7BCEDBBF2C ON travelbook (list_places_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFDE9B7B1ECC259 ON travelbook (list_fb_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFDE9B7B4ECDE009 ON travelbook (list_photos_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFDE9B7BDF08107 ON travelbook (list_souvenirs_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travelbook DROP FOREIGN KEY FK_BFDE9B7BCEDBBF2C');
        $this->addSql('ALTER TABLE travelbook DROP FOREIGN KEY FK_BFDE9B7B1ECC259');
        $this->addSql('ALTER TABLE travelbook DROP FOREIGN KEY FK_BFDE9B7B4ECDE009');
        $this->addSql('ALTER TABLE travelbook DROP FOREIGN KEY FK_BFDE9B7BDF08107');
        $this->addSql('DROP INDEX UNIQ_BFDE9B7BCEDBBF2C ON travelbook');
        $this->addSql('DROP INDEX UNIQ_BFDE9B7B1ECC259 ON travelbook');
        $this->addSql('DROP INDEX UNIQ_BFDE9B7B4ECDE009 ON travelbook');
        $this->addSql('DROP INDEX UNIQ_BFDE9B7BDF08107 ON travelbook');
        $this->addSql('ALTER TABLE travelbook DROP list_places_id, DROP list_fb_id, DROP list_photos_id, DROP list_souvenirs_id');
    }
}
