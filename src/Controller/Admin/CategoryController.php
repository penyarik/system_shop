<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends AbstractController
{

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
        private readonly TranslatorInterface $translator,
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
        }

        $form = $this->createForm(CategoryFormType::class, ['parent_category' => $parentCategory ?? null]);
        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
            && $this->isNameValid($form->getData()['category_name'])
        ) {
            if (!empty($this->productRepository->findOneByField($parentCategory, 'category_id'))) {
                $this->addFlash('flash_error', $this->translator->trans('Unable to add sub category such as parent has products'));
                return $this->redirectToRoute('admin_category_add');
            }
            $category = new Category();
            $category
                ->setName($form->getData()['category_name'])
                ->setParentId($form->getData()['parent'])
                ->setCreatedDate(new \DateTime())
                ->setUpdatedDate(new \DateTime());

            $entityManager->persist($category);
            $entityManager->flush($category);

            $this->addFlash('success', $this->translator->trans('Category has been saved successfully'));

            return $this->redirectToRoute('admin_category_add');
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

            $options = ['category' => $category];

            $options['parent_category'] = $this->categoryRepository->findPossibleParents($category->getId());

            if ($category->getParentId()) {
                $options['parent_category'] = array_merge(
                    [$this->categoryRepository->find($category->getParentId())],
                    $options['parent_category']
                );
            }

            $form = $this->createForm(CategoryFormType::class, $options);
            $form->handleRequest($request);

            if (
                $form->isSubmitted()
                && $form->isValid()
                && $this->isNameValid($form->getData()['category_name'])
            ) {
                if ($form->getData()['parent'] === $category->getId()) {
                    $this->addFlash('flash_error', $this->translator->trans('Category cant has himself as a parent'));

                    return $this->render('admin/category.html.twig', [
                        'categoryForm' => $form->createView(),
                    ]);
                }

                $category
                    ->setName($form->getData()['category_name'])
                    ->setParentId($form->getData()['parent'])
                    ->setUpdatedDate(new \DateTime());

                $entityManager->persist($category);
                $entityManager->flush($category);

                $this->addFlash('success', $this->translator->trans('Category has been edited successfully'));

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
            if (
                $this->categoryRepository->isDeletable($category->getId())
                && !$this->productRepository->findOneByField($category->getId(), 'category_id')
            ) {
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
