<?php

namespace App\Service;

use App\CustomEntity\Locale;
use App\Entity\File;
use App\Entity\Product;
use App\Entity\Translation;
use App\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;

class TranslationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslationRepository  $translationRepository,
    )
    {
    }

    public function saveTranslation(Product $product, array $productData): void
    {
        foreach (Locale::cases() as $locale) {
            $translation = new Translation();
            $translation->setLocale($locale->value)
                ->setDescriptionProduct($productData['description_' . strtolower($locale->name)])
                ->setNameProduct($productData['name_' . strtolower($locale->name)])
                ->setProduct($product);

            $this->entityManager->persist($translation);
        }

        $this->entityManager->flush();
    }

    public function updateTranslation(Product $product, array $productData): void
    {
        $translations = $this->translationRepository->findByField($product->getId(), 'product_id');

        /**
         * @var Translation $translation
         */
        foreach ($translations as $translation) {
            $translation
                ->setDescriptionProduct($productData['description_' . strtolower(Locale::tryFrom($translation->getLocale())->name)])
                ->setNameProduct($productData['name_' . strtolower(Locale::tryFrom($translation->getLocale())->name)]);

            $this->entityManager->persist($translation);
        }

        $this->entityManager->flush();
    }

    public function fillTranslationFormData(int $productId, array &$formData): void
    {
        foreach (Locale::cases() as $locale) {
            $translation = $this->translationRepository->findByLocaleAndProduct($productId, $locale->value);
            $formData['name_' . strtolower($locale->name)] = $translation->getNameProduct();
            $formData['description_' . strtolower($locale->name)] = $translation->getDescriptionProduct();
        }
    }

    public function removeTranslations(int $productId): void
    {
        $translations = $this->translationRepository->findByField($productId, 'product_id');
        /**
         * @var Translation $translation
         */
        foreach ($translations as $translation) {
            $this->translationRepository->remove($translation);
        }
    }
}