<?php

namespace App\Service;

use App\CustomEntity\Currency;
use App\CustomEntity\FileType;
use App\CustomEntity\TranslationType;
use App\Entity\Product;
use App\Entity\Seller;
use App\Repository\ProductRepository;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    public function __construct(
        private readonly SellerRepository $sellerRepository,
    )
    {
    }

    public function getSeller(?int $sellerId = null): Seller
    {
        return $sellerId ? $this->sellerRepository->find($sellerId) : $this->sellerRepository->findFirst();
    }
}