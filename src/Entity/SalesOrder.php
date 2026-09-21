<?php

namespace App\Entity;

use App\Repository\SalesOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalesOrderRepository::class)]
class SalesOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $priceHistorical = null;

    #[ORM\Column(length: 255)]
    private ?string $sku = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $ean13 = null;

    #[ORM\ManyToOne(inversedBy: 'SalesOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, SalesOrderProduct>
     */
    #[ORM\OneToMany(targetEntity: SalesOrderProduct::class, mappedBy: 'salesOrder')]
    private Collection $product;

    /**
     * @var Collection<int, SalesOrderProduct>
     */
    #[ORM\OneToMany(targetEntity: SalesOrderProduct::class, mappedBy: 'salesOrder', orphanRemoval: true)]
    private Collection $salesOrderProducts;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->salesOrderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getPriceHistorical(): ?string
    {
        return $this->priceHistorical;
    }

    public function setPriceHistorical(string $priceHistorical): static
    {
        $this->priceHistorical = $priceHistorical;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, SalesOrderProduct>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(SalesOrderProduct $product): static
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
            $product->setSalesOrder($this);
        }

        return $this;
    }

    public function removeProduct(SalesOrderProduct $product): static
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSalesOrder() === $this) {
                $product->setSalesOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SalesOrderProduct>
     */
    public function getSalesOrderProducts(): Collection
    {
        return $this->salesOrderProducts;
    }

    public function addSalesOrderProduct(SalesOrderProduct $salesOrderProduct): static
    {
        if (!$this->salesOrderProducts->contains($salesOrderProduct)) {
            $this->salesOrderProducts->add($salesOrderProduct);
            $salesOrderProduct->setSalesOrder($this);
        }

        return $this;
    }

    public function removeSalesOrderProduct(SalesOrderProduct $salesOrderProduct): static
    {
        if ($this->salesOrderProducts->removeElement($salesOrderProduct)) {
            // set the owning side to null (unless already changed)
            if ($salesOrderProduct->getSalesOrder() === $this) {
                $salesOrderProduct->setSalesOrder(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
