<?php

namespace App\Controller;

use App\CustomEntity\Locale;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\CategoryService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    public function __construct(
        private readonly UserService  $userService,
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryService $categoryService
    )
    {
    }

    #[Route('/{seller_id?}', name: 'seller_shop', requirements: ['seller_id' => '[0-9]+'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seller = $this->userService->getSeller($request->attributes->get('parent') ?? null);

        return $this->render('shop.html.twig', [
            'categories' => $this->categoryRepository->getSellerMainCategories($seller->getId(), Locale::getLocaleValue($request->getLocale())),
            'modify_access' => $this->getUser()->getId() === $seller->getUser()->getId()
        ]);
    }
}
