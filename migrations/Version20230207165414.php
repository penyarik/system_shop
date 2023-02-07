<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230207165414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE locale (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(5) NOT NULL, is_default TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('
INSERT INTO locale(name, is_default) VALUES("ru", 0);
INSERT INTO locale(name, is_default) VALUES("en", 0);
INSERT INTO locale(name, is_default) VALUES("ua", 1);

');

        $this->addSql('ALTER TABLE user ADD locale_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user 
    ADD CONSTRAINT FK_8D93D649E559DFD1 
    FOREIGN KEY (locale_id) REFERENCES locale (id)  
    ON DELETE SET NULL'
        );
        $this->addSql('CREATE INDEX IDX_8D93D649E559DFD1 ON user (locale_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E559DFD1');
        $this->addSql('DROP TABLE locale');
        $this->addSql('DROP INDEX IDX_8D93D649E559DFD1 ON user');
        $this->addSql('ALTER TABLE user DROP locale_id, DROP no');
    }
}
