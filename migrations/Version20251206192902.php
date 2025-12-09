<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251206192902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_history CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE chat_history ADD CONSTRAINT FK_6BB4BC22A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_6BB4BC22A76ED395 ON chat_history (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_history DROP FOREIGN KEY FK_6BB4BC22A76ED395');
        $this->addSql('DROP INDEX IDX_6BB4BC22A76ED395 ON chat_history');
        $this->addSql('ALTER TABLE chat_history CHANGE user_id user_id VARCHAR(255) NOT NULL');
    }
}
