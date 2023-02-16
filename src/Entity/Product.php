<?php

namespace App\Entity;

use App\CustomEntity\Currency;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(name: "IDX_PRODUCT_IS_NEW", fields: ["is_new"])]
#[ORM\Index(name: "IDX_PRODUCT_IS_TOP", fields: ["is_top"])]
#[ORM\Index(name: "IDX_D34A04AD9777D11E", fields: ["category_id"])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Column]
    private int $category_id;

    #[ORM\Column(length: 256)]
    private ?string $name = null;

    #[ORM\Column]
    private array $price = [];

    #[ORM\Column]
    private array $delivery_cost = [];

    #[ORM\Column]
    private array $delivery_cost_step = [];

    #[ORM\Column(length: 12)]
    private ?string $country = null;

    #[ORM\Column]
    private ?bool $is_top = null;

    #[ORM\Column]
    private ?bool $is_new = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seller $seller = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): array
    {
        return $this->price;
    }

    public function setPrice(array $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDeliveryCost(): array
    {
        return $this->delivery_cost;
    }

    public function setDeliveryCost(array $delivery_cost): self
    {
        $this->delivery_cost = $delivery_cost;

        return $this;
    }

    public function getDeliveryCostStep(): array
    {
        return $this->delivery_cost_step;
    }

    public function setDeliveryCostStep(array $delivery_cost_step): self
    {
        $this->delivery_cost_step = $delivery_cost_step;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function isIsTop(): ?bool
    {
        return $this->is_top;
    }

    public function setIsTop(bool $is_top): self
    {
        $this->is_top = $is_top;

        return $this;
    }

    public function isIsNew(): ?bool
    {
        return $this->is_new;
    }

    public function setIsNew(bool $is_new): self
    {
        $this->is_new = $is_new;

        return $this;
    }

    public function setCurrencyPrice(array $productData): self
    {
        $currencyPrice = [];
        $currencyDeliveryPrice = [];
        $currencyDeliveryStepPrice = [];

        foreach (Currency::cases() as $currency) {
            $currencyPrice[$currency->value] = $productData['price_'.strtolower($currency->name)];
            $currencyDeliveryPrice[$currency->value] = $productData['delivery_cost_'.strtolower($currency->name)];
            $currencyDeliveryStepPrice[$currency->value] = $productData['delivery_cost_step_'.strtolower($currency->name)];
        }

        return $this
            ->setPrice($currencyPrice)
            ->setDeliveryCost($currencyDeliveryPrice)
            ->setDeliveryCostStep($currencyDeliveryStepPrice);
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }
}
