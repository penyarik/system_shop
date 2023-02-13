<?php

namespace App\Controller\Admin;

use App\CustomEntity\Currency;
use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Form\ProductFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\FileService;
use App\Service\ProductService;
use App\Validator\ProductValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductController extends AbstractController
{

    public function __construct(
        private readonly ProductService      $productService,
        private readonly TranslatorInterface $translator,
        private readonly ProductValidator    $productValidator,
        private readonly ProductRepository   $productRepository,
    )
    {
    }

    #[Route('/admin/product/add/{category}', name: 'admin_product_add', requirements: ['category' => '[0-9]+'])]
    public function addAction(
        Request                $request,
    ): Response
    {
        $form = $this->createForm(ProductFormType::class);
        $form->handleRequest($request);

        $categoryId = $request->attributes->get('category');

        if (
            $form->isSubmitted()
            && $form->isValid()
            && $this->productValidator->validate($productData = array_merge($form->getData(), ['category_id' => $categoryId]))
        ) {

            $productId = $this->productService->saveProduct($productData);

            $this->addFlash('success', $this->translator->trans('Product has been saved successfully'));

            return $this->redirectToRoute('admin_product_edit', ['product' => $productId]);
        }

        foreach ($this->productValidator->getErrors() as $error) {
            $this->addFlash('flash_error', $error);
        }

        return $this->render('admin/product.html.twig', [
            'productForm' => $form->createView(),
            'currencies' => Currency::cases(),
        ]);
    }

    #[Route('/admin/product/edit/{product}', name: 'admin_product_edit', requirements: ['product' => '[0-9]+'])]
    public function editAction(
        Request                $request,
    ): Response
    {
        $productId = $request->attributes->get('product');
        $product = $this->productRepository->find($request->attributes->get('product'));

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $form = $this->createForm(
            ProductFormType::class,
            ['form_data' => $this->productService->getFormProductData($product)]
        );
        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $this->productService->saveProduct($form->getData(), $product);

            $this->addFlash('success', $this->translator->trans('Product has been updated successfully'));

            return $this->redirectToRoute('admin_product_edit', ['product' => $productId]);
        }

        foreach ($this->productValidator->getErrors() as $error) {
            $this->addFlash('flash_error', $error);
        }

        return $this->render('admin/product.html.twig', [
            'productForm' => $form->createView(),
            'currencies' => Currency::cases(),
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'admin_product_delete', requirements: ['id' => '[0-9]+'])]
    public function deleteAction(
        Request $request
    ): Response
    {
        if ($product = $this->productRepository->find($request->attributes->get('id'))) {
            if (
                $this->productRepository->isDeletable($product->getId())
            ) {
                $this->productService->removeProduct($product);

                $this->addFlash('success', $this->translator->trans('Product has been deleted successfully'));
            } else {
                $this->addFlash('flash_error', $this->translator->trans('Product has sub categories or products!'));
            }

            return $this->redirectToRoute('admin');
        } else {
            throw new NotFoundHttpException();
        }
    }
}
