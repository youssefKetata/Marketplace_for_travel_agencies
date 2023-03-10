<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220094553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Api (id INT AUTO_INCREMENT NOT NULL, baseUrl VARCHAR(45) NOT NULL, apiKeyValue VARCHAR(45) NOT NULL, login VARCHAR(45) NOT NULL, password VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ApiProduct (id INT AUTO_INCREMENT NOT NULL, api_id INT DEFAULT NULL, name VARCHAR(45) NOT NULL, idProductFromApi VARCHAR(45) DEFAULT NULL, productType_id INT NOT NULL, INDEX IDX_2C6E9EE291BE1328 (productType_id), INDEX IDX_2C6E9EE254963938 (api_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apiproduct_selleroffer (apiproduct_id INT NOT NULL, selleroffer_id INT NOT NULL, INDEX IDX_28866BD75CC623C (apiproduct_id), INDEX IDX_28866BDFC802C82 (selleroffer_id), PRIMARY KEY(apiproduct_id, selleroffer_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ApiProductClick (id INT AUTO_INCREMENT NOT NULL, traveler_id INT NOT NULL, date DATETIME NOT NULL, ipTraveler VARCHAR(45) DEFAULT NULL, ipLocation VARCHAR(45) DEFAULT NULL, INDEX IDX_FC4628859BBE8A3 (traveler_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MarketSubscriptionRequest (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, email VARCHAR(45) NOT NULL, website VARCHAR(45) NOT NULL, address VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Offer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, nbProductTypes INT NOT NULL, nbDays INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE OfferProductType (id INT AUTO_INCREMENT NOT NULL, offer_id INT NOT NULL, maxItems VARCHAR(45) NOT NULL, price DOUBLE PRECISION NOT NULL, productType_id INT NOT NULL, INDEX IDX_EFEA72F453C674EE (offer_id), INDEX IDX_EFEA72F491BE1328 (productType_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ProductType (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Seller (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, user_id INT NOT NULL, api_id INT DEFAULT NULL, name VARCHAR(45) NOT NULL, website VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, INDEX IDX_FCB6D6CA8BAC62AF (city_id), UNIQUE INDEX UNIQ_FCB6D6CAA76ED395 (user_id), UNIQUE INDEX UNIQ_FCB6D6CA54963938 (api_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SellerOffer (id INT AUTO_INCREMENT NOT NULL, offer_id INT NOT NULL, seller_id INT NOT NULL, creationDate DATETIME NOT NULL, startDate DATETIME NOT NULL, INDEX IDX_3157921653C674EE (offer_id), INDEX IDX_315792168DE820D9 (seller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Traveler (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, firstName VARCHAR(45) NOT NULL, lastName VARCHAR(45) NOT NULL, address VARCHAR(45) NOT NULL, INDEX IDX_913790408BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ApiProduct ADD CONSTRAINT FK_2C6E9EE291BE1328 FOREIGN KEY (productType_id) REFERENCES ProductType (id)');
        $this->addSql('ALTER TABLE ApiProduct ADD CONSTRAINT FK_2C6E9EE254963938 FOREIGN KEY (api_id) REFERENCES Api (id)');
        $this->addSql('ALTER TABLE apiproduct_selleroffer ADD CONSTRAINT FK_28866BD75CC623C FOREIGN KEY (apiproduct_id) REFERENCES ApiProduct (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE apiproduct_selleroffer ADD CONSTRAINT FK_28866BDFC802C82 FOREIGN KEY (selleroffer_id) REFERENCES SellerOffer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ApiProductClick ADD CONSTRAINT FK_FC4628859BBE8A3 FOREIGN KEY (traveler_id) REFERENCES Traveler (id)');
        $this->addSql('ALTER TABLE OfferProductType ADD CONSTRAINT FK_EFEA72F453C674EE FOREIGN KEY (offer_id) REFERENCES Offer (id)');
        $this->addSql('ALTER TABLE OfferProductType ADD CONSTRAINT FK_EFEA72F491BE1328 FOREIGN KEY (productType_id) REFERENCES ProductType (id)');
        $this->addSql('ALTER TABLE Seller ADD CONSTRAINT FK_FCB6D6CA8BAC62AF FOREIGN KEY (city_id) REFERENCES City (id)');
        $this->addSql('ALTER TABLE Seller ADD CONSTRAINT FK_FCB6D6CAA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE Seller ADD CONSTRAINT FK_FCB6D6CA54963938 FOREIGN KEY (api_id) REFERENCES Api (id)');
        $this->addSql('ALTER TABLE SellerOffer ADD CONSTRAINT FK_3157921653C674EE FOREIGN KEY (offer_id) REFERENCES Offer (id)');
        $this->addSql('ALTER TABLE SellerOffer ADD CONSTRAINT FK_315792168DE820D9 FOREIGN KEY (seller_id) REFERENCES Seller (id)');
        $this->addSql('ALTER TABLE Traveler ADD CONSTRAINT FK_913790408BAC62AF FOREIGN KEY (city_id) REFERENCES City (id)');
        $this->addSql('ALTER TABLE City ADD CONSTRAINT FK_8D69AD0AF026BB7C FOREIGN KEY (country_code) REFERENCES Country (code)');
        $this->addSql('ALTER TABLE Country ADD CONSTRAINT FK_9CCEF0FA16C569B FOREIGN KEY (continent_code) REFERENCES Continent (code)');
        $this->addSql('ALTER TABLE Country ADD CONSTRAINT FK_9CCEF0FAFDA273EC FOREIGN KEY (currency_code) REFERENCES Currency (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ApiProduct DROP FOREIGN KEY FK_2C6E9EE291BE1328');
        $this->addSql('ALTER TABLE ApiProduct DROP FOREIGN KEY FK_2C6E9EE254963938');
        $this->addSql('ALTER TABLE apiproduct_selleroffer DROP FOREIGN KEY FK_28866BD75CC623C');
        $this->addSql('ALTER TABLE apiproduct_selleroffer DROP FOREIGN KEY FK_28866BDFC802C82');
        $this->addSql('ALTER TABLE ApiProductClick DROP FOREIGN KEY FK_FC4628859BBE8A3');
        $this->addSql('ALTER TABLE OfferProductType DROP FOREIGN KEY FK_EFEA72F453C674EE');
        $this->addSql('ALTER TABLE OfferProductType DROP FOREIGN KEY FK_EFEA72F491BE1328');
        $this->addSql('ALTER TABLE Seller DROP FOREIGN KEY FK_FCB6D6CA8BAC62AF');
        $this->addSql('ALTER TABLE Seller DROP FOREIGN KEY FK_FCB6D6CAA76ED395');
        $this->addSql('ALTER TABLE Seller DROP FOREIGN KEY FK_FCB6D6CA54963938');
        $this->addSql('ALTER TABLE SellerOffer DROP FOREIGN KEY FK_3157921653C674EE');
        $this->addSql('ALTER TABLE SellerOffer DROP FOREIGN KEY FK_315792168DE820D9');
        $this->addSql('ALTER TABLE Traveler DROP FOREIGN KEY FK_913790408BAC62AF');
        $this->addSql('DROP TABLE Api');
        $this->addSql('DROP TABLE ApiProduct');
        $this->addSql('DROP TABLE apiproduct_selleroffer');
        $this->addSql('DROP TABLE ApiProductClick');
        $this->addSql('DROP TABLE MarketSubscriptionRequest');
        $this->addSql('DROP TABLE Offer');
        $this->addSql('DROP TABLE OfferProductType');
        $this->addSql('DROP TABLE ProductType');
        $this->addSql('DROP TABLE Seller');
        $this->addSql('DROP TABLE SellerOffer');
        $this->addSql('DROP TABLE Traveler');
        $this->addSql('ALTER TABLE City DROP FOREIGN KEY FK_8D69AD0AF026BB7C');
        $this->addSql('ALTER TABLE Country DROP FOREIGN KEY FK_9CCEF0FA16C569B');
        $this->addSql('ALTER TABLE Country DROP FOREIGN KEY FK_9CCEF0FAFDA273EC');
    }
}
