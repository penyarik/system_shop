<?php

namespace App\Service;

use App\CustomEntity\Currency;
use App\CustomEntity\FileType;
use App\CustomEntity\TranslationType;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository     $categoryRepository,
    )
    {
    }

    public function getSellerMainCategories(int $sellerId)
    {
        return $this->categoryRepository->findOneByField();

    }
}