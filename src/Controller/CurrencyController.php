<?php

namespace App\Controller;

use App\CustomEntity\Currency;
use App\CustomEntity\Locale;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyController extends AbstractController
{
    public function __construct(
        private readonly UserRepository   $userRepository,
    )
    {
    }

    #[Route('/currency/{currency}', name: 'currency', requirements: ['currency' => '[0-9]+'])]
    public function currency(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($currency = Currency::tryFrom($request->attributes->get('currency'))) {
            if ($email = $request->getSession()->get('_security.last_username')) {
                $user = $this->userRepository->findOneByField($email, 'email');
                $user->setCurrency($currency->value);
                $entityManager->flush($user);
            }

            $request->getSession()->set('_currency', $currency->name);

            return (new Response())->setStatusCode(Response::HTTP_CREATED);
        } else {
            throw new NotFoundHttpException();
        }
    }
}
