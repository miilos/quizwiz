<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225184046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token LONGTEXT NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5F37A13BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('DROP INDEX IDX_FT_TITLE ON quiz');
        $this->addSql('DROP INDEX IDX_FT_DESCRIPTION ON quiz');
        $this->addSql('DROP INDEX IDX_FT_DISPLAY_NAME ON tag');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BA76ED395');
        $this->addSql('DROP TABLE token');
        $this->addSql('CREATE FULLTEXT INDEX IDX_FT_DISPLAY_NAME ON tag (display_name)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_FT_TITLE ON quiz (title)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_FT_DESCRIPTION ON quiz (description)');
    }
}
