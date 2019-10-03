<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191002195426 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_buffer CHANGE lang lang VARCHAR(20) NOT NULL, CHANGE type type VARCHAR(20) NOT NULL, CHANGE league league VARCHAR(30) NOT NULL, CHANGE team1_name team1_name VARCHAR(50) NOT NULL, CHANGE team2_name team2_name VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_buffer CHANGE lang lang SMALLINT NOT NULL, CHANGE type type SMALLINT NOT NULL, CHANGE league league SMALLINT NOT NULL, CHANGE team1_name team1_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE team2_name team2_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
