<?php

namespace App\Validator;

use App\Repository\CategoryRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductValidator
{
    private array $errors = [];

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function validate(array $data): bool
    {
        if (!$this->isCategoryValid($data['category_id'])) {
            return false;
        }

        return true;
    }

    private function isCategoryValid(int $categoryId): bool
    {
        if (!empty($this->categoryRepository->findOneByField($categoryId, 'parent_id'))) {
            $this->addError($this->translator->trans('Category has child categories. Unable to add product'));
            return false;
        }

        if (empty($this->categoryRepository->findOneByField($categoryId, 'id'))) {
            $this->addError($this->translator->trans('Category does not exist. Unable to add product'));
            return false;
        }

        return true;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}