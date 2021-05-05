<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210323040303 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE examination (id INT AUTO_INCREMENT NOT NULL, matter_id INT NOT NULL, exam_id INT NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_CCDAABC5D614E59F (matter_id), INDEX IDX_CCDAABC5578D5E91 (exam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE examination ADD CONSTRAINT FK_CCDAABC5D614E59F FOREIGN KEY (matter_id) REFERENCES matter (id)');
        $this->addSql('ALTER TABLE examination ADD CONSTRAINT FK_CCDAABC5578D5E91 FOREIGN KEY (exam_id) REFERENCES exam (id)');
        $this->addSql('ALTER TABLE claim ADD examination_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE27DAD0CFBF FOREIGN KEY (examination_id) REFERENCES examination (id)');
        $this->addSql('CREATE INDEX IDX_A769DE27DAD0CFBF ON claim (examination_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE claim DROP FOREIGN KEY FK_A769DE27DAD0CFBF');
        $this->addSql('DROP TABLE examination');
        $this->addSql('DROP INDEX IDX_A769DE27DAD0CFBF ON claim');
        $this->addSql('ALTER TABLE claim DROP examination_id');
    }
}
