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

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $totalPriceHistorical = null;

    #[ORM\ManyToOne(inversedBy: 'SalesOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, SalesOrderProduct>
     */
    #[ORM\OneToMany(targetEntity: SalesOrderProduct::class, mappedBy: 'salesOrder', orphanRemoval: true)]
    private Collection $salesOrderProducts;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->salesOrderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPriceHistorical(): ?string
    {
        return $this->totalPriceHistorical;
    }

    public function setTotalPriceHistorical(string $totalPriceHistorical): static
    {
        $this->totalPriceHistorical = $totalPriceHistorical;

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
