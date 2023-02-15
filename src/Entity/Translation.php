<?php

namespace App\Entity;

use App\Repository\TranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
class Translation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description_product = null;

    #[ORM\Column(length: 256)]
    private ?string $name_product = null;

    #[ORM\Column(length: 1)]
    private ?int $locale = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(nullable: false)]
    private int $product_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescriptionProduct(): ?string
    {
        return $this->description_product;
    }

    public function setDescriptionProduct(string $description_product): self
    {
        $this->description_product = $description_product;

        return $this;
    }

    public function getNameProduct(): ?string
    {
        return $this->name_product;
    }

    public function getLocale(): int
    {
        return $this->locale;
    }

    public function setLocale(int $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function setNameProduct(string $name_product): self
    {
        $this->name_product = $name_product;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
