<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260102234117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_quiz (tag_id INT NOT NULL, quiz_id INT NOT NULL, INDEX IDX_4A1D4524BAD26311 (tag_id), INDEX IDX_4A1D4524853CD175 (quiz_id), PRIMARY KEY(tag_id, quiz_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_quiz ADD CONSTRAINT FK_4A1D4524BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_quiz ADD CONSTRAINT FK_4A1D4524853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_quiz DROP FOREIGN KEY FK_4A1D4524BAD26311');
        $this->addSql('ALTER TABLE tag_quiz DROP FOREIGN KEY FK_4A1D4524853CD175');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_quiz');
    }
}
