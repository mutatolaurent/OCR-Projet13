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

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $historicalPrice = null;

    #[ORM\ManyToOne(inversedBy: 'salesOrderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SalesOrder $salesOrder = null;

    #[ORM\ManyToOne(inversedBy: 'salesOrderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

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

    public function getHistoricalPrice(): ?string
    {
        return $this->historicalPrice;
    }

    public function setHistoricalPrice(string $historicalPrice): static
    {
        $this->historicalPrice = $historicalPrice;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

}
