<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218200809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE translation ADD name_category VARCHAR(256) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE translation DROP name_category');
        $this->addSql('ALTER TABLE seller DROP FOREIGN KEY FK_FB1AD3FCA76ED395');
        $this->addSql('ALTER TABLE seller ADD CONSTRAINT FK_FB1AD3FCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category CHANGE updated_date updated_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE created_date created_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT IDX_CAT_PARENT_ID FOREIGN KEY (parent_id) REFERENCES category (id) ON UPDATE NO ACTION');
        $this->addSql('ALTER TABLE user CHANGE locale_id locale_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_8D93D649E559DFD1 ON user (locale_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9777D11E FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION');
    }
}
