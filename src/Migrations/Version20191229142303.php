<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191229142303 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trick ADD main_image_id INT DEFAULT NULL, DROP image_name');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EE4873418 FOREIGN KEY (main_image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91EE4873418 ON trick (main_image_id)');
        $this->addSql('ALTER TABLE trick ADD trick_group VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EE4873418');
        $this->addSql('DROP INDEX UNIQ_D8F0A91EE4873418 ON trick');
        $this->addSql('ALTER TABLE trick ADD image_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP main_image_id');
        $this->addSql('ALTER TABLE trick DROP trick_group');
    }
}
