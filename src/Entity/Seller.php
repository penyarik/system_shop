<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
#[ORM\Index(name: "INDEX_USER_ID", fields: ["user_id"])]
class Seller
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $welcome_message = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $sub_name = null;

    #[ORM\Column(length: 7, nullable: false)]
    private ?string $main_color = null;

    #[ORM\Column(length: 7, nullable: false)]
    private ?string $sub_color = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: false)]
    private int $user_id;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWelcomeMessage(): ?string
    {
        return $this->welcome_message;
    }

    public function setWelcomeMessage(string $welcome_message): self
    {
        $this->welcome_message = $welcome_message;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSubName(): ?string
    {
        return $this->sub_name;
    }

    public function setSubName(string $sub_name): self
    {
        $this->sub_name = $sub_name;

        return $this;
    }

    public function getMainColor(): ?string
    {
        return $this->main_color;
    }

    public function setMainColor(string $main_color): self
    {
        $this->main_color = $main_color;

        return $this;
    }

    public function getSubColor(): ?string
    {
        return $this->sub_color;
    }

    public function setSubColor(string $sub_color): self
    {
        $this->sub_color = $sub_color;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
