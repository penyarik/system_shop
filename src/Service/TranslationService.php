<?php

namespace App\Service;

use App\CustomEntity\Locale;
use App\CustomEntity\TranslationType;
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

    public function saveTranslation(int $entityId, TranslationType $translationType, array $data): void
    {
        foreach (Locale::cases() as $locale) {
            $translation = new Translation();

            $translation->setLocale($locale->value)
                ->setEntityId($entityId)
                ->setEntityType($translationType->value);

            switch ($translationType) {
                case TranslationType::PRODUCT:
                    $translation
                        ->setDescriptionProduct($data['description_' . strtolower($locale->name)])
                        ->setNameProduct($data['name_' . strtolower($locale->name)]);

                    break;
                case TranslationType::CATEGORY:
                    $translation
                        ->setDescriptionCategory($data['description_' . strtolower($locale->name)])
                        ->setNameCategory($data['name_' . strtolower($locale->name)]);

                    break;
            }

            $this->entityManager->persist($translation);
        }

        $this->entityManager->flush();
    }

    public function updateTranslation(int $entityId, TranslationType $translationType, array $data): void
    {
        $translations = $this->translationRepository->findByEntityIdAndEntityType($entityId, $translationType->value);

        /**
         * @var Translation $translation
         */
        foreach ($translations as $translation) {

            switch ($translationType) {
                case TranslationType::PRODUCT:
                    $translation
                        ->setDescriptionProduct($data['description_' . strtolower(Locale::tryFrom($translation->getLocale())->name)])
                        ->setNameProduct($data['name_' . strtolower(Locale::tryFrom($translation->getLocale())->name)]);

                    break;
                case TranslationType::CATEGORY:
                    $translation
                        ->setDescriptionCategory($data['description_' . strtolower(Locale::tryFrom($translation->getLocale())->name)])
                        ->setNameCategory($data['name_' . strtolower(Locale::tryFrom($translation->getLocale())->name)]);
                    break;
            }

            $this->entityManager->persist($translation);
        }

        $this->entityManager->flush();
    }

    public function fillTranslationFormData(int $entityId, TranslationType $translationType, array &$formData): void
    {
        foreach (Locale::cases() as $locale) {
            $translation = $this->translationRepository->findByLocaleAndEntityIdtAndEntityType(
                $entityId,
                $translationType->value,
                $locale->value
            );

            switch ($translationType) {
                case TranslationType::PRODUCT:
                    $formData['name_' . strtolower($locale->name)] = $translation->getNameProduct();
                    $formData['description_' . strtolower($locale->name)] = $translation->getDescriptionProduct();

                    break;
                case TranslationType::CATEGORY:
                    $formData['description_' . strtolower($locale->name)] = $translation->getDescriptionCategory();
                    $formData['name_' . strtolower($locale->name)] = $translation->getNameCategory();

                    break;
            }
        }
    }

    public function removeTranslations(int $entityId, TranslationType $translationType): void
    {
        $translations = $this->translationRepository->findByEntityIdAndEntityType($entityId, $translationType->value);
        /**
         * @var Translation $translation
         */
        foreach ($translations as $translation) {
            $this->translationRepository->remove($translation);
        }
    }
}