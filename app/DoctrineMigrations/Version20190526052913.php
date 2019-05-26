<?php declare(strict_types=1);

namespace Application\Migrations;

use AppBundle\Entity\AppRole;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190526052913 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users_roles (userId CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', roleId INT NOT NULL, INDEX IDX_51498A8E64B64DCC (userId), INDEX IDX_51498A8EB8C2FD88 (roleId), PRIMARY KEY(userId, roleId)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8E64B64DCC FOREIGN KEY (userId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EB8C2FD88 FOREIGN KEY (roleId) REFERENCES roles (id)');
        $this->addSql('insert into roles (id, name) values (1, \''.AppRole::PASSENGER_NAME.'\')');
        $this->addSql('insert into roles (id, name) values (2, \''.AppRole::DRIVER_NAME.'\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EB8C2FD88');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE roles');
    }
}
