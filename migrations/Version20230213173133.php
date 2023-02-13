<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213173133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB2F9FB5D');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE7216051');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8A7A7C89');
        $this->addSql('CREATE TABLE translation (id INT AUTO_INCREMENT NOT NULL, description_product LONGTEXT NOT NULL, name_product VARCHAR(256) NOT NULL, locale INT NOT NULL, INDEX INDEX_LOCALE (locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE translation_en');
        $this->addSql('DROP TABLE translation_ru');
        $this->addSql('DROP TABLE translation_ua');
        $this->addSql('DROP INDEX UNIQ_D34A04ADB2F9FB5D ON product');
        $this->addSql('DROP INDEX UNIQ_D34A04ADE7216051 ON product');
        $this->addSql('DROP INDEX UNIQ_D34A04AD8A7A7C89 ON product');
        $this->addSql('ALTER TABLE product ADD translation_id INT NOT NULL, DROP translation_ua_id, DROP translation_ru_id, DROP translation_en_id');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_PRODUCT_TRANSLATION FOREIGN KEY (translation_id) REFERENCES translation (id) ON DELETE RESTRICT ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PRODUCT_TRANSLATION ON product (translation_id)');
    }

    public function down(Schema $schema): void
    {
    }
}
