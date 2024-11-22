<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119133118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alert (id INT AUTO_INCREMENT NOT NULL, sensor_id INT NOT NULL, message VARCHAR(255) NOT NULL, severity VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_17FD46C1A247991F (sensor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sensor (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sensor_data (id INT AUTO_INCREMENT NOT NULL, sensor_id INT DEFAULT NULL, nitrogen DOUBLE PRECISION DEFAULT NULL, phosphorus DOUBLE PRECISION DEFAULT NULL, potassium DOUBLE PRECISION DEFAULT NULL, temperature DOUBLE PRECISION DEFAULT NULL, ph DOUBLE PRECISION DEFAULT NULL, timestamp DATETIME DEFAULT NULL, INDEX IDX_801762CCA247991F (sensor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C1A247991F FOREIGN KEY (sensor_id) REFERENCES sensor (id)');
        $this->addSql('ALTER TABLE sensor_data ADD CONSTRAINT FK_801762CCA247991F FOREIGN KEY (sensor_id) REFERENCES sensor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C1A247991F');
        $this->addSql('ALTER TABLE sensor_data DROP FOREIGN KEY FK_801762CCA247991F');
        $this->addSql('DROP TABLE alert');
        $this->addSql('DROP TABLE sensor');
        $this->addSql('DROP TABLE sensor_data');
    }
}
