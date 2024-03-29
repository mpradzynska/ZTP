<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220905155234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE image_id image_id INT NOT NULL');
        $this->addSql('ALTER TABLE galleries CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE images CHANGE gallery_id gallery_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images CHANGE gallery_id gallery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `comments` CHANGE image_id image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `galleries` CHANGE user_id user_id INT DEFAULT NULL');
    }
}
