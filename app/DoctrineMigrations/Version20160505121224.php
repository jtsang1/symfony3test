<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160505121224 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user ADD youtube_channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A6479A3BFE8 FOREIGN KEY (youtube_channel_id) REFERENCES youtube_channel (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A3BFE8 ON fos_user (youtube_channel_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A6479A3BFE8');
        $this->addSql('DROP INDEX UNIQ_957A6479A3BFE8 ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP youtube_channel_id');
    }
}
