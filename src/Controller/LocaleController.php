<?php

namespace App\Controller;

use App\Repository\LocaleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LocaleRepository $localeRepository
    ) {
    }

    #[Route('/locale/{locale}', name: 'locale', requirements: ['locale' => 'ru|en|ua'])]
    public function locale(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($locale = $this->localeRepository->findOneByField($request->attributes->get('locale'), 'name')) {

            if ($email = $request->getSession()->get('_security.last_username')) {
                $user = $this->userRepository->findOneByField($email, 'email');
                $user->setLocaleId($locale->getId());
                $entityManager->flush($user);
            }

            $request->getSession()->set('_locale', $locale->getName());

            return (new Response())->setStatusCode(Response::HTTP_CREATED);

        } else {
            throw new NotFoundHttpException();
        }
    }
}
