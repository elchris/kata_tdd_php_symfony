<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190603124420 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE locations (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, created_utc DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rides (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_utc DATETIME NOT NULL, passengerId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', departureId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_9D4620A3B0FAA905 (passengerId), INDEX IDX_9D4620A36E9E2929 (departureId), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A3B0FAA905 FOREIGN KEY (passengerId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A36E9E2929 FOREIGN KEY (departureId) REFERENCES locations (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_9D4620A36E9E2929');
        $this->addSql('DROP TABLE locations');
        $this->addSql('DROP TABLE rides');
    }
}
