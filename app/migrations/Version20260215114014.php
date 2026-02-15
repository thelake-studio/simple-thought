<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215114014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE goal_log (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, value INT NOT NULL, goal_id INT NOT NULL, INDEX IDX_BE88A257667D1AFE (goal_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE goal_log ADD CONSTRAINT FK_BE88A257667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE goal_log DROP FOREIGN KEY FK_BE88A257667D1AFE');
        $this->addSql('DROP TABLE goal_log');
    }
}
