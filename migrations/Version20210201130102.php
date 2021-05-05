<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210201130102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE academic_year (id INT AUTO_INCREMENT NOT NULL, coded VARCHAR(255) NOT NULL, wording VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, claim_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_DADD4A257096A49F (claim_id), INDEX IDX_DADD4A25F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE claim (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT DEFAULT NULL, author_id INT NOT NULL, created_at DATETIME NOT NULL, content LONGTEXT NOT NULL, wording VARCHAR(255) NOT NULL, capture VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, INDEX IDX_A769DE27456C5646 (evaluation_id), INDEX IDX_A769DE27F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, semester_id INT NOT NULL, matter_id INT NOT NULL, type VARCHAR(255) NOT NULL, wording VARCHAR(255) NOT NULL, archived TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1323A5754A798B6F (semester_id), INDEX IDX_1323A575D614E59F (matter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, coded VARCHAR(255) NOT NULL, wording VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matter (id INT AUTO_INCREMENT NOT NULL, coded VARCHAR(255) NOT NULL, wording VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matter_specialty (matter_id INT NOT NULL, specialty_id INT NOT NULL, level_id INT NOT NULL, INDEX IDX_F634AF61D614E59F (matter_id), INDEX IDX_F634AF619A353316 (specialty_id), INDEX IDX_F634AF615FB14BA7 (level_id), PRIMARY KEY(matter_id, specialty_id, level_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration (user_id INT NOT NULL, level_id INT NOT NULL, specialty_id INT NOT NULL, semester_id INT NOT NULL, INDEX IDX_62A8A7A7A76ED395 (user_id), INDEX IDX_62A8A7A75FB14BA7 (level_id), INDEX IDX_62A8A7A79A353316 (specialty_id), INDEX IDX_62A8A7A74A798B6F (semester_id), PRIMARY KEY(user_id, level_id, specialty_id, semester_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semester (id INT AUTO_INCREMENT NOT NULL, academic_year_id INT NOT NULL, coded VARCHAR(255) NOT NULL, wording VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_F7388EEDC54F3401 (academic_year_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty (id INT AUTO_INCREMENT NOT NULL, coded VARCHAR(255) NOT NULL, wording VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, profile VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_matter (user_id INT NOT NULL, matter_id INT NOT NULL, INDEX IDX_D58C8052A76ED395 (user_id), INDEX IDX_D58C8052D614E59F (matter_id), PRIMARY KEY(user_id, matter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A257096A49F FOREIGN KEY (claim_id) REFERENCES claim (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE27456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE27F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5754A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575D614E59F FOREIGN KEY (matter_id) REFERENCES matter (id)');
        $this->addSql('ALTER TABLE matter_specialty ADD CONSTRAINT FK_F634AF61D614E59F FOREIGN KEY (matter_id) REFERENCES matter (id)');
        $this->addSql('ALTER TABLE matter_specialty ADD CONSTRAINT FK_F634AF619A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id)');
        $this->addSql('ALTER TABLE matter_specialty ADD CONSTRAINT FK_F634AF615FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A75FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A79A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A74A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDC54F3401 FOREIGN KEY (academic_year_id) REFERENCES academic_year (id)');
        $this->addSql('ALTER TABLE user_matter ADD CONSTRAINT FK_D58C8052A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_matter ADD CONSTRAINT FK_D58C8052D614E59F FOREIGN KEY (matter_id) REFERENCES matter (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDC54F3401');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A257096A49F');
        $this->addSql('ALTER TABLE claim DROP FOREIGN KEY FK_A769DE27456C5646');
        $this->addSql('ALTER TABLE matter_specialty DROP FOREIGN KEY FK_F634AF615FB14BA7');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A75FB14BA7');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575D614E59F');
        $this->addSql('ALTER TABLE matter_specialty DROP FOREIGN KEY FK_F634AF61D614E59F');
        $this->addSql('ALTER TABLE user_matter DROP FOREIGN KEY FK_D58C8052D614E59F');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5754A798B6F');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A74A798B6F');
        $this->addSql('ALTER TABLE matter_specialty DROP FOREIGN KEY FK_F634AF619A353316');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A79A353316');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25F675F31B');
        $this->addSql('ALTER TABLE claim DROP FOREIGN KEY FK_A769DE27F675F31B');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A7A76ED395');
        $this->addSql('ALTER TABLE user_matter DROP FOREIGN KEY FK_D58C8052A76ED395');
        $this->addSql('DROP TABLE academic_year');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE claim');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE matter');
        $this->addSql('DROP TABLE matter_specialty');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE semester');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_matter');
    }
}
