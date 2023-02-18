<?php

namespace App\Controller\Admin;

use App\CustomEntity\FileType;
use App\CustomEntity\TranslationType;
use App\Entity\Category;
use App\Entity\Seller;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SellerRepository;
use App\Service\FileService;
use App\Service\TranslationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends AbstractController
{
    private Seller $seller;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
        private readonly TranslatorInterface $translator,
        private readonly SellerRepository $sellerRepository,
        private readonly FileService $fileService,
        private readonly TranslationService $translationService,
    )
    {
    }

    #[Route('/admin/category/add/{parent?}', name: 'admin_category_add', requirements: ['parent' => '[0-9]+'])]
    public function addAction(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        if ($parentCategory = $request->attributes->get('parent')) {
            $parentCategory = $this->categoryRepository->find($parentCategory);
            if (!$this->categoryRepository->findByIdAndSeller($this->sellerRepository->findOneByField($this->getUser()->getId(), 'user_id')->getId(), $parentCategory->getId())) {
                throw new NotAcceptableHttpException();
            }
        }

        $form = $this->createForm(CategoryFormType::class, ['parent_category' => $parentCategory ?? null, 'is_update' => false]);
        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            if (!empty($this->productRepository->findOneByField($parentCategory, 'category_id'))) {
                $this->addFlash('flash_error', $this->translator->trans('Unable to add sub category such as parent has products'));
                return $this->redirectToRoute('admin_category_add');
            }
            $connection = $entityManager->getConnection();
            $connection->beginTransaction();

            try {
                $category = new Category();
                $category
                    ->setName($form->getData()['name_en'])
                    ->setParentId($form->getData()['parent'])
                    ->setCreatedDate(new \DateTime())
                    ->setUpdatedDate(new \DateTime())
                    ->setSeller($this->sellerRepository->findOneByField($this->getUser()->getId(), 'user_id'));

                $entityManager->persist($category);
                $entityManager->flush();

                $this->fileService->saveFile($category->getId(), [$form->getData()['image']], FileType::CATEGORY_IMAGE, FileService::IMAGE_PATH);
                $this->fileService->saveFile($category->getId(), [$form->getData()['icon']], FileType::CATEGORY_ICON, FileService::IMAGE_PATH);

                $this->translationService->saveTranslation($category->getId(), TranslationType::CATEGORY, $form->getData());

                $this->addFlash('success', $this->translator->trans('Category has been saved successfully'));
                $connection->commit();
            } catch (\Throwable $exception) {
                $connection->rollBack();
                $this->addFlash('flash_error', $this->translator->trans($exception->getMessage()));
                return $this->redirectToRoute('admin_category_add');
            }

            return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
        }

        return $this->render('admin/category.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/edit/{id}', name: 'admin_category_edit', requirements: ['id' => '[0-9]+'])]
    public function editAction(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        if ($category = $this->categoryRepository->find($request->attributes->get('id'))) {

            if (!$this->categoryRepository->findByIdAndSeller(
                $this->sellerRepository->findOneByField($this->getUser()->getId(), 'user_id')->getId(),
                $category->getId()
            )) {
                throw new NotAcceptableHttpException();
            }

            $options = ['category' => $category, 'is_update' => true];

            $options['parent_category'] = $this->categoryRepository->findPossibleParents($category->getId());

            if ($category->getParentId()) {
                $options['parent_category'] = array_merge(
                    [$this->categoryRepository->find($category->getParentId())],
                    $options['parent_category']
                );
            }

            $this->translationService->fillTranslationFormData($category->getId(), TranslationType::CATEGORY, $options);

            $form = $this->createForm(CategoryFormType::class, $options);
            $form->handleRequest($request);

            if (
                $form->isSubmitted()
                && $form->isValid()
            ) {
                if ($form->getData()['parent'] === $category->getId()) {
                    $this->addFlash('flash_error', $this->translator->trans('Category cant has himself as a parent'));

                    return $this->render('admin/category.html.twig', [
                        'categoryForm' => $form->createView(),
                    ]);
                }

                $connection = $entityManager->getConnection();
                $connection->beginTransaction();

                try {
                    $category
                        ->setName($form->getData()['name_en'])
                        ->setParentId($form->getData()['parent'])
                        ->setUpdatedDate(new \DateTime());

                    $entityManager->persist($category);
                    $entityManager->flush();

                    if (!empty($form->getData()['image'])) {
                        $this->fileService->updateFile($category->getId(), $form->getData()['image'], FileType::CATEGORY_IMAGE, FileService::IMAGE_PATH);
                    }

                    if (!empty($form->getData()['icon'])) {
                        $this->fileService->updateFile($category->getId(), $form->getData()['icon'], FileType::CATEGORY_ICON, FileService::IMAGE_PATH);
                    }

                    $this->translationService->updateTranslation($category->getId(), TranslationType::CATEGORY, $form->getData());


                    $connection->commit();

                    $this->addFlash('success', $this->translator->trans('Category has been edited successfully'));
                } catch (\Throwable $exception) {
                    $connection->rollBack();
                    $this->addFlash('flash_error', $this->translator->trans($exception->getMessage()));
                    return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
                }

                return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
            }

            return $this->render('admin/category.html.twig', [
                'categoryForm' => $form->createView(),
            ]);
        } else {
            throw new NotFoundHttpException();
        }
    }

    private function isNameValid(string $name): bool
    {
        if (!$this->categoryRepository->findOneByField($name, 'name')) {
           return true;
        } else {
            $this->addFlash('flash_error', $this->translator->trans('Category name is duplicated'));
            return false;
        }
    }

    #[Route('/admin/category/delete/{id}', name: 'admin_category_delete', requirements: ['id' => '[0-9]+'])]
    public function deleteAction(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        if ($category = $this->categoryRepository->find($request->attributes->get('id'))) {

            if (!$this->categoryRepository->findByIdAndSeller($this->sellerRepository->findOneByField($this->getUser()->getId(), 'user_id')->getId(), $category->getId())) {
                throw new NotAcceptableHttpException();
            }

            if (
                $this->categoryRepository->isDeletable($category->getId())
                && !$this->productRepository->findOneByField($category->getId(), 'category_id')
            ) {
                $this->fileService->removeFiles($category->getId(), FileType::CATEGORY_ICON);
                $this->fileService->removeFiles($category->getId(), FileType::CATEGORY_IMAGE);

                $this->translationService->removeTranslations($category->getId(), TranslationType::CATEGORY);

                $entityManager->remove($category);
                $entityManager->flush();

                $this->addFlash('success', $this->translator->trans('Category has been deleted successfully'));
            } else {
                $this->addFlash('flash_error', $this->translator->trans('Category has sub categories or products!'));
            }

            return $this->redirectToRoute('admin_category_add');
        } else {
            throw new NotFoundHttpException();
        }
    }
}
