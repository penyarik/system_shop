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
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FileService            $fileService,
        private readonly TranslationService     $translationService,
        private readonly SellerRepository       $sellerRepository,
        private readonly CategoryRepository     $categoryRepository,
    )
    {
    }

    public function getFormProductData(Product $product): array
    {
        $formData = [];

        $formData['amount'] = $product->getAmount();
        $formData['is_top'] = $product->isIsTop();
        $formData['is_new'] = $product->isIsNew();
        $formData['country'] = $product->getCountry();

        foreach (Currency::cases() as $currency) {
            $formData['price_' . strtolower($currency->name)] = $product->getPrice()[$currency->value];
            $formData['delivery_cost_' . strtolower($currency->name)] = $product->getPrice()[$currency->value];
            $formData['delivery_cost_step_' . strtolower($currency->name)] = $product->getPrice()[$currency->value];
        }

        $this->translationService->fillTranslationFormData($product->getId(), $formData);

        return $formData;

    }

    public function saveProduct(array $productData, UserInterface $user, Product $product = null): int
    {
        $isUpdate = !empty($product);

        $sellerId = $this->sellerRepository->findOneByField($user->getId(), 'user_id')?->getId();

        if (!$sellerId || !$this->categoryRepository->findByIdAndSeller($sellerId, $productData['category_id'] ?? $product->getCategoryId())) {
            throw new NotAcceptableHttpException();
        }

        if ($isUpdate && !$this->productRepository->findByIdAndSeller($sellerId, $product->getId())) {
            throw new NotAcceptableHttpException();
        }

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $product = $product ?? new Product();
            $product->setName($productData['name_en'])
                ->setAmount($productData['amount'])
                ->setCountry($productData['country'])
                ->setCurrencyPrice($productData)
                ->setIsTop($productData['is_top'])
                ->setIsNew($productData['is_new'])
                ->setCategoryId($productData['category_id'] ?? $product->getCategoryId())
                ->setSeller($this->sellerRepository->findOneByField($user->getId(), 'user_id'));


            $this->entityManager->persist($product);
            $this->entityManager->flush();

            if ($isUpdate) {
                $this->translationService->updateTranslation($product->getId(), TranslationType::PRODUCT, $productData);
                if (!empty($productData['image'])) {
                    $this->fileService->updateFile($product->getId(), $productData['image'], FileType::PRODUCT_GALLERY, FileService::IMAGE_PATH);
                }
                if (!empty($productData['attachment'])) {
                    $this->fileService->updateFile($product->getId(), $productData['attachment'], FileType::PRODUCT_ATTACHMENT, FileService::ATTACHMENT_PATH);
                }
            } else {
                $this->fileService->saveFile($product->getId(), $productData['image'], FileType::PRODUCT_GALLERY, FileService::IMAGE_PATH);
                $this->fileService->saveFile($product->getId(), $productData['attachment'], FileType::PRODUCT_ATTACHMENT, FileService::ATTACHMENT_PATH);
                $this->translationService->saveTranslation($product->getId(), TranslationType::PRODUCT, $productData);
            }

            $connection->commit();

            return $product->getId();
        } catch (\Throwable $e) {
            $connection->rollBack();
            throw new \Exception('Something went wrong during saving product');
        }
    }

    public function removeProduct(Product $product, UserInterface $user): void
    {
        $sellerId = $this->sellerRepository->findOneByField($user->getId(), 'user_id')?->getId();

        if (!$sellerId || !$this->productRepository->findByIdAndSeller($sellerId, $product->getId())) {
            throw new NotAcceptableHttpException();
        }
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $this->translationService->removeTranslations($product->getId(), TranslationType::PRODUCT);

            $this->fileService->removeFiles($product->getId(), FileType::PRODUCT_GALLERY);
            $this->fileService->removeFiles($product->getId(), FileType::PRODUCT_ATTACHMENT);

            $this->entityManager->remove($product);
            $this->entityManager->flush();
            $connection->commit();
        } catch (\Throwable $ex) {
            $connection->rollBack();
            throw new \Exception('Smth went wrong during deletion');
        }
    }
}