<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190603140005 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rides ADD driverId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A323F411D5 FOREIGN KEY (driverId) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_9D4620A323F411D5 ON rides (driverId)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_9D4620A323F411D5');
        $this->addSql('DROP INDEX IDX_9D4620A323F411D5 ON rides');
        $this->addSql('ALTER TABLE rides DROP driverId');
    }
}
