<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113214515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_FT_DESC ON quiz');
        $this->addSql('DROP INDEX IDX_FT_TITLE ON quiz');
        $this->addSql('ALTER TABLE quiz_attempt ADD correct_answer_count INT NOT NULL, ADD incorrect_answer_count INT NOT NULL, ADD percentage_score DOUBLE PRECISION NOT NULL');
        $this->addSql('DROP INDEX IDX_FT_DISPLAY_NAME ON tag');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE FULLTEXT INDEX IDX_FT_DISPLAY_NAME ON tag (display_name)');
        $this->addSql('ALTER TABLE quiz_attempt DROP correct_answer_count, DROP incorrect_answer_count, DROP percentage_score');
        $this->addSql('CREATE FULLTEXT INDEX IDX_FT_DESC ON quiz (description)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_FT_TITLE ON quiz (title)');
    }
}
