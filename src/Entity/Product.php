<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\Column(length: 255)]
    private ?string $shortDescr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $longDescr = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $priceCurrent = null;

    #[ORM\Column(length: 255)]
    private ?string $sku = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $ean13 = null;

    #[ORM\Column(length: 60)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $active = null;

    /**
     * @var Collection<int, BasketProduct>
     */
    #[ORM\OneToMany(targetEntity: BasketProduct::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $basketProducts;

    /**
     * @var Collection<int, SalesOrderProduct>
     */
    #[ORM\OneToMany(targetEntity: SalesOrderProduct::class, mappedBy: 'product')]
    private Collection $salesOrderProducts;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->basketProducts = new ArrayCollection();
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

    public function getShortDescr(): ?string
    {
        return $this->shortDescr;
    }

    public function setShortDescr(string $shortDescr): static
    {
        $this->shortDescr = $shortDescr;

        return $this;
    }

    public function getLongDescr(): ?string
    {
        return $this->longDescr;
    }

    public function setLongDescr(?string $longDescr): static
    {
        $this->longDescr = $longDescr;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPriceCurrent(): ?string
    {
        return $this->priceCurrent;
    }

    public function setPriceCurrent(string $priceCurrent): static
    {
        $this->priceCurrent = $priceCurrent;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, BasketProduct>
     */
    public function getBasketProducts(): Collection
    {
        return $this->basketProducts;
    }

    public function addBasketProduct(BasketProduct $basketProduct): static
    {
        if (!$this->basketProducts->contains($basketProduct)) {
            $this->basketProducts->add($basketProduct);
            $basketProduct->setProduct($this);
        }

        return $this;
    }

    public function removeBasketProduct(BasketProduct $basketProduct): static
    {
        if ($this->basketProducts->removeElement($basketProduct)) {
            // set the owning side to null (unless already changed)
            if ($basketProduct->getProduct() === $this) {
                $basketProduct->setProduct(null);
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
            $salesOrderProduct->setProduct($this);
        }

        return $this;
    }

    public function removeSalesOrderProduct(SalesOrderProduct $salesOrderProduct): static
    {
        if ($this->salesOrderProducts->removeElement($salesOrderProduct)) {
            // set the owning side to null (unless already changed)
            if ($salesOrderProduct->getProduct() === $this) {
                $salesOrderProduct->setProduct(null);
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
