<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113112847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entry_activity (entry_id INT NOT NULL, activity_id INT NOT NULL, INDEX IDX_C233E912BA364942 (entry_id), INDEX IDX_C233E91281C06096 (activity_id), PRIMARY KEY (entry_id, activity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE entry_tag (entry_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F035C9E5BA364942 (entry_id), INDEX IDX_F035C9E5BAD26311 (tag_id), PRIMARY KEY (entry_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE entry_activity ADD CONSTRAINT FK_C233E912BA364942 FOREIGN KEY (entry_id) REFERENCES entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entry_activity ADD CONSTRAINT FK_C233E91281C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entry_tag ADD CONSTRAINT FK_F035C9E5BA364942 FOREIGN KEY (entry_id) REFERENCES entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entry_tag ADD CONSTRAINT FK_F035C9E5BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entry_activity DROP FOREIGN KEY FK_C233E912BA364942');
        $this->addSql('ALTER TABLE entry_activity DROP FOREIGN KEY FK_C233E91281C06096');
        $this->addSql('ALTER TABLE entry_tag DROP FOREIGN KEY FK_F035C9E5BA364942');
        $this->addSql('ALTER TABLE entry_tag DROP FOREIGN KEY FK_F035C9E5BAD26311');
        $this->addSql('DROP TABLE entry_activity');
        $this->addSql('DROP TABLE entry_tag');
    }
}
