<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218181552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F4584665A');
        $this->addSql('DROP INDEX IDX_B469456F4584665A ON translation');
        $this->addSql('ALTER TABLE translation ADD description_category LONGTEXT NOT NULL, ADD entity_type INT NOT NULL, CHANGE product_id entity_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_TRANSLATION_ENTITY_ID ON translation (entity_id)');
        $this->addSql('CREATE INDEX IDX_TRANSLATION_ENTITY_TYPE ON translation (entity_type)');
        $this->addSql('ALTER TABLE translation RENAME INDEX index_locale TO IDX_TRANSLATION_LOCALE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE locale_id locale_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_8D93D649E559DFD1 ON user (locale_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9777D11E FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION');
        $this->addSql('ALTER TABLE seller DROP FOREIGN KEY FK_FB1AD3FCA76ED395');
        $this->addSql('ALTER TABLE seller ADD CONSTRAINT FK_FB1AD3FCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_TRANSLATIO_ENTITY_ID ON translation');
        $this->addSql('DROP INDEX IDX_TRANSLATIO_ENTITY_TYPE ON translation');
        $this->addSql('ALTER TABLE translation ADD product_id INT NOT NULL, DROP description_category, DROP entity_id, DROP entity_type');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B469456F4584665A ON translation (product_id)');
        $this->addSql('ALTER TABLE translation RENAME INDEX idx_translation_locale TO INDEX_LOCALE');
        $this->addSql('ALTER TABLE category DROP description, CHANGE updated_date updated_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE created_date created_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT IDX_CAT_PARENT_ID FOREIGN KEY (parent_id) REFERENCES category (id) ON UPDATE NO ACTION');
    }
}
