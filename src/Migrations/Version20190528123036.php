<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190528123036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), UNIQUE INDEX UNIQ_64C19C1D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brand (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1C52F9585E237E06 (name), UNIQUE INDEX UNIQ_1C52F958D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE opinion (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, reduction_id INT UNSIGNED NOT NULL, comment LONGTEXT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(64) DEFAULT NULL, email VARCHAR(64) DEFAULT NULL, client_ip VARCHAR(16) DEFAULT NULL, UNIQUE INDEX UNIQ_AB02B027D17F50A6 (uuid), INDEX IDX_AB02B027A76ED395 (user_id), INDEX IDX_AB02B027C03CB092 (reduction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, brand_id INT UNSIGNED NOT NULL, title VARCHAR(64) NOT NULL, slug VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, region VARCHAR(2) NOT NULL, department VARCHAR(3) DEFAULT NULL, municipality VARCHAR(64) DEFAULT NULL, is_big_deal TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(64) DEFAULT NULL, email VARCHAR(64) DEFAULT NULL, client_ip VARCHAR(16) DEFAULT NULL, UNIQUE INDEX UNIQ_B1E754682B36786B (title), UNIQUE INDEX UNIQ_B1E75468989D9B62 (slug), UNIQUE INDEX UNIQ_B1E75468D17F50A6 (uuid), INDEX IDX_B1E75468A76ED395 (user_id), INDEX IDX_B1E7546844F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reduction_category (reduction_id INT UNSIGNED NOT NULL, category_id INT UNSIGNED NOT NULL, INDEX IDX_9CADA68C03CB092 (reduction_id), INDEX IDX_9CADA6812469DE2 (category_id), PRIMARY KEY(reduction_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(64) NOT NULL, identity VARCHAR(64) NOT NULL, email VARCHAR(64) DEFAULT NULL, phone_number VARCHAR(32) DEFAULT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, roles JSON NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B027A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B027C03CB092 FOREIGN KEY (reduction_id) REFERENCES reduction (id)');
        $this->addSql('ALTER TABLE reduction ADD CONSTRAINT FK_B1E75468A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reduction ADD CONSTRAINT FK_B1E7546844F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE reduction_category ADD CONSTRAINT FK_9CADA68C03CB092 FOREIGN KEY (reduction_id) REFERENCES reduction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reduction_category ADD CONSTRAINT FK_9CADA6812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reduction_category DROP FOREIGN KEY FK_9CADA6812469DE2');
        $this->addSql('ALTER TABLE reduction DROP FOREIGN KEY FK_B1E7546844F5D008');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B027C03CB092');
        $this->addSql('ALTER TABLE reduction_category DROP FOREIGN KEY FK_9CADA68C03CB092');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B027A76ED395');
        $this->addSql('ALTER TABLE reduction DROP FOREIGN KEY FK_B1E75468A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE opinion');
        $this->addSql('DROP TABLE reduction');
        $this->addSql('DROP TABLE reduction_category');
        $this->addSql('DROP TABLE user');
    }
}
