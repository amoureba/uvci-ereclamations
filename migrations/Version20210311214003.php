<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210311214003 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exam (id INT AUTO_INCREMENT NOT NULL, semester_id INT NOT NULL, level_id INT NOT NULL, specialty_id INT NOT NULL, session VARCHAR(255) NOT NULL, INDEX IDX_38BBA6C64A798B6F (semester_id), INDEX IDX_38BBA6C65FB14BA7 (level_id), INDEX IDX_38BBA6C69A353316 (specialty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C64A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C65FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C69A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE exam');
    }
}
