<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220627114711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, genre_id INT NOT NULL, platform_id INT NOT NULL, name VARCHAR(255) NOT NULL, cover VARCHAR(100) NOT NULL, INDEX IDX_232B318C4296D31F (genre_id), INDEX IDX_232B318CFFE6496F (platform_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE platform (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, picture VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C4296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CFFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C4296D31F');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CFFE6496F');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE platform');
    }
}
