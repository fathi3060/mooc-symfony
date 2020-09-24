<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207185944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oc_application (id INT AUTO_INCREMENT NOT NULL, advert_id INT NOT NULL, author VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_39F85DD8D07ECCB6 (advert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oc_advert (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, published TINYINT(1) NOT NULL, date DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, email VARCHAR(180) DEFAULT NULL, content LONGTEXT NOT NULL, nb_applications INT NOT NULL, slug VARCHAR(255) NOT NULL, ip VARCHAR(180) DEFAULT NULL, UNIQUE INDEX UNIQ_B1931752B36786B (title), UNIQUE INDEX UNIQ_B193175989D9B62 (slug), UNIQUE INDEX UNIQ_B1931753DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oc_advert_category (advert_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_435EA006D07ECCB6 (advert_id), INDEX IDX_435EA00612469DE2 (category_id), PRIMARY KEY(advert_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oc_advert_skill (id INT AUTO_INCREMENT NOT NULL, advert_id INT NOT NULL, skill_id INT NOT NULL, level VARCHAR(255) NOT NULL, INDEX IDX_32EFF25BD07ECCB6 (advert_id), INDEX IDX_32EFF25B5585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oc_image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oc_application ADD CONSTRAINT FK_39F85DD8D07ECCB6 FOREIGN KEY (advert_id) REFERENCES oc_advert (id)');
        $this->addSql('ALTER TABLE oc_advert ADD CONSTRAINT FK_B1931753DA5256D FOREIGN KEY (image_id) REFERENCES oc_image (id)');
        $this->addSql('ALTER TABLE oc_advert_category ADD CONSTRAINT FK_435EA006D07ECCB6 FOREIGN KEY (advert_id) REFERENCES oc_advert (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oc_advert_category ADD CONSTRAINT FK_435EA00612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oc_advert_skill ADD CONSTRAINT FK_32EFF25BD07ECCB6 FOREIGN KEY (advert_id) REFERENCES oc_advert (id)');
        $this->addSql('ALTER TABLE oc_advert_skill ADD CONSTRAINT FK_32EFF25B5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oc_advert_category DROP FOREIGN KEY FK_435EA00612469DE2');
        $this->addSql('ALTER TABLE oc_application DROP FOREIGN KEY FK_39F85DD8D07ECCB6');
        $this->addSql('ALTER TABLE oc_advert_category DROP FOREIGN KEY FK_435EA006D07ECCB6');
        $this->addSql('ALTER TABLE oc_advert_skill DROP FOREIGN KEY FK_32EFF25BD07ECCB6');
        $this->addSql('ALTER TABLE oc_advert_skill DROP FOREIGN KEY FK_32EFF25B5585C142');
        $this->addSql('ALTER TABLE oc_advert DROP FOREIGN KEY FK_B1931753DA5256D');
        $this->addSql('DROP TABLE oc_application');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE oc_advert');
        $this->addSql('DROP TABLE oc_advert_category');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE oc_advert_skill');
        $this->addSql('DROP TABLE oc_image');
    }
}
