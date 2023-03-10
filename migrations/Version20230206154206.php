<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230206154206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE City (id INT AUTO_INCREMENT NOT NULL, country_code VARCHAR(2) DEFAULT NULL, name VARCHAR(50) NOT NULL, latitude NUMERIC(16, 12) DEFAULT NULL, longitude NUMERIC(16, 12) DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_8D69AD0AF026BB7C (country_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Continent (code VARCHAR(2) NOT NULL, name VARCHAR(50) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Country (code VARCHAR(2) NOT NULL, continent_code VARCHAR(2) DEFAULT NULL, currency_code VARCHAR(3) DEFAULT NULL, alpha3 VARCHAR(3) NOT NULL, name VARCHAR(64) NOT NULL, phone_code SMALLINT DEFAULT NULL, capital VARCHAR(64) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_9CCEF0FAC065E6E4 (alpha3), UNIQUE INDEX UNIQ_9CCEF0FA5E237E06 (name), INDEX IDX_9CCEF0FA16C569B (continent_code), INDEX IDX_9CCEF0FAFDA273EC (currency_code), PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Currency (code VARCHAR(3) NOT NULL, name VARCHAR(128) NOT NULL, symbol VARCHAR(16) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE DataTableColumn (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, datatableName VARCHAR(64) NOT NULL, selector VARCHAR(64) NOT NULL, sortable TINYINT(1) NOT NULL, visible TINYINT(1) NOT NULL, width VARCHAR(64) DEFAULT NULL, display_order SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE FileData (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, directoryPath VARCHAR(128) NOT NULL, extension VARCHAR(32) NOT NULL, alt VARCHAR(255) DEFAULT NULL, ordre SMALLINT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MenuItemAdmin (id VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, route VARCHAR(255) NOT NULL, displayOrder SMALLINT NOT NULL, parent VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Parameter (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, isVerified TINYINT(1) NOT NULL, username VARCHAR(255) NOT NULL, active TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_2DA17977E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), INDEX general_translations_lookup_idx (object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE City ADD CONSTRAINT FK_8D69AD0AF026BB7C FOREIGN KEY (country_code) REFERENCES Country (code)');
        $this->addSql('ALTER TABLE Country ADD CONSTRAINT FK_9CCEF0FA16C569B FOREIGN KEY (continent_code) REFERENCES Continent (code)');
        $this->addSql('ALTER TABLE Country ADD CONSTRAINT FK_9CCEF0FAFDA273EC FOREIGN KEY (currency_code) REFERENCES Currency (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE City DROP FOREIGN KEY FK_8D69AD0AF026BB7C');
        $this->addSql('ALTER TABLE Country DROP FOREIGN KEY FK_9CCEF0FA16C569B');
        $this->addSql('ALTER TABLE Country DROP FOREIGN KEY FK_9CCEF0FAFDA273EC');
        $this->addSql('DROP TABLE City');
        $this->addSql('DROP TABLE Continent');
        $this->addSql('DROP TABLE Country');
        $this->addSql('DROP TABLE Currency');
        $this->addSql('DROP TABLE DataTableColumn');
        $this->addSql('DROP TABLE FileData');
        $this->addSql('DROP TABLE MenuItemAdmin');
        $this->addSql('DROP TABLE Parameter');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
