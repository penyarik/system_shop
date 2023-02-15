<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212150746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) NOT NULL, path VARCHAR(1000) NOT NULL, entity_name VARCHAR(255) NOT NULL, entity_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, translation_ua_id INT NOT NULL, translation_ru_id INT NOT NULL, translation_en_id INT NOT NULL, name VARCHAR(256) NOT NULL, price JSON NOT NULL, delivery_cost JSON NOT NULL, delivery_cost_step JSON NOT NULL, country VARCHAR(12) NOT NULL, is_top TINYINT(1) NOT NULL, INDEX IDX_D34A04AD9777D11E (category_id), UNIQUE INDEX UNIQ_D34A04AD8A7A7C89 (translation_ua_id), UNIQUE INDEX UNIQ_D34A04ADE7216051 (translation_ru_id), UNIQUE INDEX UNIQ_D34A04ADB2F9FB5D (translation_en_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translation_en (id INT AUTO_INCREMENT NOT NULL, description_product LONGTEXT NOT NULL, name_product VARCHAR(256) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translation_ru (id INT AUTO_INCREMENT NOT NULL, description_product LONGTEXT NOT NULL, name_product VARCHAR(256) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translation_ua (id INT AUTO_INCREMENT NOT NULL, description_product LONGTEXT NOT NULL, name_product VARCHAR(256) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9777D11E FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD8A7A7C89 FOREIGN KEY (translation_ua_id) REFERENCES translation_ua (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE7216051 FOREIGN KEY (translation_ru_id) REFERENCES translation_ru (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB2F9FB5D FOREIGN KEY (translation_en_id) REFERENCES translation_en (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD9777D11E');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8A7A7C89');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE7216051');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB2F9FB5D');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE translation_en');
        $this->addSql('DROP TABLE translation_ru');
        $this->addSql('DROP TABLE translation_ua');
        $this->addSql('ALTER TABLE category CHANGE updated_date updated_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE created_date created_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT IDX_CAT_PARENT_ID FOREIGN KEY (parent_id) REFERENCES category (id) ON UPDATE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CAT_PARENT_ID ON category (parent_id)');
        $this->addSql('ALTER TABLE user CHANGE locale_id locale_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_8D93D649E559DFD1 ON user (locale_id)');
    }
}
