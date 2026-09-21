<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260921120202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sales_order (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, price_historical NUMERIC(8, 2) NOT NULL, sku VARCHAR(255) NOT NULL, ean13 VARCHAR(13) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_36D222EA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sales_order_product (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, historical_price NUMERIC(8, 2) NOT NULL, sales_order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_E016DB55C023F51C (sales_order_id), INDEX IDX_E016DB554584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE sales_order ADD CONSTRAINT FK_36D222EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sales_order_product ADD CONSTRAINT FK_E016DB55C023F51C FOREIGN KEY (sales_order_id) REFERENCES sales_order (id)');
        $this->addSql('ALTER TABLE sales_order_product ADD CONSTRAINT FK_E016DB554584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY `FK_6EEAA67DA76ED395`');
        $this->addSql('ALTER TABLE commande_product DROP FOREIGN KEY `FK_25F1760D4584665A`');
        $this->addSql('ALTER TABLE commande_product DROP FOREIGN KEY `FK_25F1760D82EA2E54`');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_product');
        $this->addSql('ALTER TABLE basket DROP relation');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY `FK_17ED14B41BE1FB52`');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY `FK_17ED14B44584665A`');
        $this->addSql('ALTER TABLE basket_product ADD id INT AUTO_INCREMENT NOT NULL, ADD quantity INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B41BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id)');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, price_historical NUMERIC(8, 2) NOT NULL, sku VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, ean13 VARCHAR(13) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, user_id INT NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commande_product (commande_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_25F1760D82EA2E54 (commande_id), INDEX IDX_25F1760D4584665A (product_id), PRIMARY KEY (commande_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT `FK_6EEAA67DA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commande_product ADD CONSTRAINT `FK_25F1760D4584665A` FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_product ADD CONSTRAINT `FK_25F1760D82EA2E54` FOREIGN KEY (commande_id) REFERENCES commande (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sales_order DROP FOREIGN KEY FK_36D222EA76ED395');
        $this->addSql('ALTER TABLE sales_order_product DROP FOREIGN KEY FK_E016DB55C023F51C');
        $this->addSql('ALTER TABLE sales_order_product DROP FOREIGN KEY FK_E016DB554584665A');
        $this->addSql('DROP TABLE sales_order');
        $this->addSql('DROP TABLE sales_order_product');
        $this->addSql('ALTER TABLE basket ADD relation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B41BE1FB52');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B44584665A');
        $this->addSql('ALTER TABLE basket_product MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE basket_product DROP id, DROP quantity, DROP PRIMARY KEY, ADD PRIMARY KEY (basket_id, product_id)');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT `FK_17ED14B41BE1FB52` FOREIGN KEY (basket_id) REFERENCES basket (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT `FK_17ED14B44584665A` FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
