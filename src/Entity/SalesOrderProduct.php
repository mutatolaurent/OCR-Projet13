<?php

namespace App\Entity;

use App\Repository\SalesOrderProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalesOrderProductRepository::class)]
class SalesOrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $historicalPrice = null;

    #[ORM\Column(length: 255)]
    private ?string $sku = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $ean13 = null;

    #[ORM\ManyToOne(inversedBy: 'salesOrderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SalesOrder $salesOrder = null;

    #[ORM\Column(nullable: true)]
    private ?int $historicalIdProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHistoricalPrice(): ?string
    {
        return $this->historicalPrice;
    }

    public function setHistoricalPrice(string $historicalPrice): static
    {
        $this->historicalPrice = $historicalPrice;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getEan13(): ?string
    {
        return $this->ean13;
    }

    public function setEan13(?string $ean13): static
    {
        $this->ean13 = $ean13;

        return $this;
    }

    public function getSalesOrder(): ?SalesOrder
    {
        return $this->salesOrder;
    }

    public function setSalesOrder(?SalesOrder $salesOrder): static
    {
        $this->salesOrder = $salesOrder;

        return $this;
    }

    public function getHistoricalIdProduct(): ?int
    {
        return $this->historicalIdProduct;
    }

    public function setHistoricalIdProduct(?int $historicalIdProduct): static
    {
        $this->historicalIdProduct = $historicalIdProduct;
        return $this;
    }
}
