<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'basket', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, BasketProduct>
     */
    #[ORM\OneToMany(targetEntity: BasketProduct::class, mappedBy: 'basket')]
    private Collection $product;

    /**
     * @var Collection<int, BasketProduct>
     */
    #[ORM\OneToMany(targetEntity: BasketProduct::class, mappedBy: 'basket', orphanRemoval: true)]
    private Collection $basketProducts;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->basketProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, BasketProduct>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(BasketProduct $product): static
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
            $product->setBasket($this);
        }

        return $this;
    }

    public function removeProduct(BasketProduct $product): static
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBasket() === $this) {
                $product->setBasket(null);
            }
        }

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
            $basketProduct->setBasket($this);
        }

        return $this;
    }

    public function removeBasketProduct(BasketProduct $basketProduct): static
    {
        if ($this->basketProducts->removeElement($basketProduct)) {
            // set the owning side to null (unless already changed)
            if ($basketProduct->getBasket() === $this) {
                $basketProduct->setBasket(null);
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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
