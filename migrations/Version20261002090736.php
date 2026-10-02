<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002090736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales_order DROP sku, DROP ean13, CHANGE price_historical total_price_historical NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE sales_order_product DROP FOREIGN KEY `FK_E016DB554584665A`');
        $this->addSql('DROP INDEX IDX_E016DB554584665A ON sales_order_product');
        $this->addSql('ALTER TABLE sales_order_product ADD sku VARCHAR(255) NOT NULL, ADD ean13 VARCHAR(13) DEFAULT NULL, ADD historical_id_product INT DEFAULT NULL, DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales_order ADD sku VARCHAR(255) NOT NULL, ADD ean13 VARCHAR(13) DEFAULT NULL, CHANGE total_price_historical price_historical NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE sales_order_product ADD product_id INT NOT NULL, DROP sku, DROP ean13, DROP historical_id_product');
        $this->addSql('ALTER TABLE sales_order_product ADD CONSTRAINT `FK_E016DB554584665A` FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E016DB554584665A ON sales_order_product (product_id)');
    }
}
