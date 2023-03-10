<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220115447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE MarketSubscriptionRequest ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE MarketSubscriptionRequest ADD CONSTRAINT FK_7976E848BAC62AF FOREIGN KEY (city_id) REFERENCES City (id)');
        $this->addSql('CREATE INDEX IDX_7976E848BAC62AF ON MarketSubscriptionRequest (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE MarketSubscriptionRequest DROP FOREIGN KEY FK_7976E848BAC62AF');
        $this->addSql('DROP INDEX IDX_7976E848BAC62AF ON MarketSubscriptionRequest');
        $this->addSql('ALTER TABLE MarketSubscriptionRequest DROP city_id');
    }
}
