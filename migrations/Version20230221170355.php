<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221170355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ApiProduitClick (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ApiProductClick ADD apiProduct_id INT NOT NULL');
        $this->addSql('ALTER TABLE ApiProductClick ADD CONSTRAINT FK_FC462883A9161EC FOREIGN KEY (apiProduct_id) REFERENCES ApiProduct (id)');
        $this->addSql('CREATE INDEX IDX_FC462883A9161EC ON ApiProductClick (apiProduct_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ApiProduitClick');
        $this->addSql('ALTER TABLE ApiProductClick DROP FOREIGN KEY FK_FC462883A9161EC');
        $this->addSql('DROP INDEX IDX_FC462883A9161EC ON ApiProductClick');
        $this->addSql('ALTER TABLE ApiProductClick DROP apiProduct_id');
    }
}
