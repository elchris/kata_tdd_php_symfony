<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180131035821 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE locations (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', lat DOUBLE PRECISION NOT NULL, `longitude` DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_roles (userId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', roleId INT NOT NULL, INDEX IDX_51498A8E64B64DCC (userId), INDEX IDX_51498A8EB8C2FD88 (roleId), PRIMARY KEY(userId, roleId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rides (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', passengerId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', driverId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', departureId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', destinationId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_9D4620A3B0FAA905 (passengerId), INDEX IDX_9D4620A323F411D5 (driverId), INDEX IDX_9D4620A36E9E2929 (departureId), INDEX IDX_9D4620A3BF3434FC (destinationId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rideEvents (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL, rideId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', userId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', eventTypeId INT DEFAULT NULL, INDEX IDX_893034F0F23620C7 (rideId), INDEX IDX_893034F064B64DCC (userId), INDEX IDX_893034F0577BCC16 (eventTypeId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rideEventTypes (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8E64B64DCC FOREIGN KEY (userId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EB8C2FD88 FOREIGN KEY (roleId) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A3B0FAA905 FOREIGN KEY (passengerId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A323F411D5 FOREIGN KEY (driverId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A36E9E2929 FOREIGN KEY (departureId) REFERENCES locations (id)');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A3BF3434FC FOREIGN KEY (destinationId) REFERENCES locations (id)');
        $this->addSql('ALTER TABLE rideEvents ADD CONSTRAINT FK_893034F0F23620C7 FOREIGN KEY (rideId) REFERENCES rides (id)');
        $this->addSql('ALTER TABLE rideEvents ADD CONSTRAINT FK_893034F064B64DCC FOREIGN KEY (userId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rideEvents ADD CONSTRAINT FK_893034F0577BCC16 FOREIGN KEY (eventTypeId) REFERENCES rideEventTypes (id)');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_9D4620A36E9E2929');
        $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_9D4620A3BF3434FC');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EB8C2FD88');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8E64B64DCC');
        $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_9D4620A3B0FAA905');
        $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_9D4620A323F411D5');
        $this->addSql('ALTER TABLE rideEvents DROP FOREIGN KEY FK_893034F064B64DCC');
        $this->addSql('ALTER TABLE rideEvents DROP FOREIGN KEY FK_893034F0F23620C7');
        $this->addSql('ALTER TABLE rideEvents DROP FOREIGN KEY FK_893034F0577BCC16');
        $this->addSql('DROP TABLE locations');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE rides');
        $this->addSql('DROP TABLE rideEvents');
        $this->addSql('DROP TABLE rideEventTypes');
    }
}
