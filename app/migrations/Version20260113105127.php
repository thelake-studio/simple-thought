<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113105127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entry (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL, mood_value_snapshot SMALLINT NOT NULL, user_id INT NOT NULL, emotion_id INT DEFAULT NULL, INDEX IDX_2B219D70A76ED395 (user_id), INDEX IDX_2B219D701EE4A582 (emotion_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D701EE4A582 FOREIGN KEY (emotion_id) REFERENCES emotion (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70A76ED395');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D701EE4A582');
        $this->addSql('DROP TABLE entry');
    }
}
