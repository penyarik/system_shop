<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213182819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD amount INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE updated_date updated_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE created_date created_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT IDX_CAT_PARENT_ID FOREIGN KEY (parent_id) REFERENCES category (id) ON UPDATE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CAT_PARENT_ID ON category (parent_id)');
        $this->addSql('ALTER TABLE user CHANGE locale_id locale_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_8D93D649E559DFD1 ON user (locale_id)');
        $this->addSql('CREATE INDEX INDEX_LOCALE ON translation (locale)');
        $this->addSql('ALTER TABLE product DROP amount');
        $this->addSql('CREATE INDEX IDX_PRODUCT_IS_NEW ON product (is_new)');
        $this->addSql('CREATE INDEX IDX_PRODUCT_IS_TOP ON product (is_top)');
        $this->addSql('ALTER TABLE product RENAME INDEX idx_d34a04ad12469de2 TO IDX_D34A04AD9777D11E');
        $this->addSql('CREATE UNIQUE INDEX IDX_FILE_NAME ON file (name)');
    }
}
