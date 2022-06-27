<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220627115545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game CHANGE cover cover VARCHAR(100) DEFAULT \'blank_cover.jpg\' NOT NULL');
        $this->addSql('ALTER TABLE platform CHANGE picture picture VARCHAR(100) DEFAULT \'blank_picture.jpg\' NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE avatar avatar VARCHAR(100) DEFAULT \'blank_avatar.jpg\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game CHANGE cover cover VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE platform CHANGE picture picture VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE avatar avatar VARCHAR(100) NOT NULL');
    }
}
