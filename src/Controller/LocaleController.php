<?php

namespace App\Controller;

use App\CustomEntity\Locale;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    public function __construct(
        private readonly UserRepository   $userRepository,
    )
    {
    }

    #[Route('/locale/{locale}', name: 'locale', requirements: ['locale' => '[0-9]+'])]
    public function locale(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($locale = Locale::tryFrom($request->attributes->get('locale'))) {
            if ($email = $request->getSession()->get('_security.last_username')) {
                $user = $this->userRepository->findOneByField($email, 'email');
                $user->setLocaleId($locale->value);
                $entityManager->flush($user);
            }

            $request->getSession()->set('_locale', strtolower($locale->name));

            return (new Response())->setStatusCode(Response::HTTP_CREATED);
        } else {
            throw new NotFoundHttpException();
        }
    }
}
