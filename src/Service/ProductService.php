<?php

namespace App\Service;

use App\CustomEntity\Currency;
use App\CustomEntity\Locale;
use App\Entity\Product;
use App\Repository\FileRepository;
use App\Repository\ProductRepository;
use App\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FileService            $fileService,
        private readonly TranslationService     $translationService,
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

    public function saveProduct(array $productData, Product $product = null): int
    {
        $isUpdate = !empty($product);
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
                ->setCategoryId($productData['category_id'] ?? $product->getCategoryId());

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            if ($isUpdate) {
                $this->translationService->updateTranslation($product, $productData);
                if (!empty($productData['image'])) {
                    $this->fileService->updateFile($product->getId(), $productData['image'], true);
                }
                if (!empty($productData['attachment'])) {
                    $this->fileService->updateFile($product->getId(), $productData['attachment'], false);
                }
            } else {
                $this->fileService->saveFile($product->getId(), $productData['image'], true);
                $this->fileService->saveFile($product->getId(), $productData['attachment'], false);
                $this->translationService->saveTranslation($product, $productData);
            }

            $connection->commit();

            return $product->getId();
        } catch (\Throwable $e) {
            $connection->rollBack();
            throw new \Exception('Something went wrong during saving product');
        }
    }

    public function removeProduct(Product $product): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $this->translationService->removeTranslations($product->getId());
            $this->fileService->removeFiles($product->getId());
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            $connection->commit();
        } catch (\Throwable $ex) {
            $connection->rollBack();
            throw new \Exception('Smth went wrong during deletion');
        }
    }
}