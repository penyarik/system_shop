<?php

namespace App\Entity;

use App\Repository\TranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\Index(name: "IDX_TRANSLATION_ENTITY_ID", fields: ["entity_id"])]
#[ORM\Index(name: "IDX_TRANSLATION_ENTITY_TYPE", fields: ["entity_type"])]
#[ORM\Index(name: "IDX_TRANSLATION_LOCALE", fields: ["locale"])]
class Translation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description_product = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description_category = null;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $name_category = null;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $name_product = null;

    #[ORM\Column(length: 1, nullable: false)]
    private ?int $locale = null;

    #[ORM\Column(nullable: false)]
    private ?int $entity_id = null;

    #[ORM\Column(length: 1, nullable: false)]
    private ?int $entity_type = null;

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

    public function getDescriptionCategory(): ?string
    {
        return $this->description_category;
    }

    public function setDescriptionCategory(string $description): self
    {
        $this->description_category = $description;

        return $this;
    }

    public function getNameCategory(): ?string
    {
        return $this->name_category;
    }

    public function setNameCategory(string $name): self
    {
        $this->name_category = $name;

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

    public function getEntityId(): ?int
    {
        return $this->entity_id;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entity_id = $entityId;

        return $this;
    }

    public function getEntityType(): ?int
    {
        return $this->entity_type;
    }

    public function setEntityType(int $entityType): self
    {
        $this->entity_type = $entityType;

        return $this;
    }
}
