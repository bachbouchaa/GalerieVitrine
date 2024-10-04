<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004001136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE painting (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, my_painting_collection_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, creation_year INTEGER NOT NULL, description VARCHAR(255) NOT NULL, style VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_66B9EBA01651BB9A FOREIGN KEY (my_painting_collection_id) REFERENCES my_painting_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_66B9EBA01651BB9A ON painting (my_painting_collection_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE painting');
    }
}
