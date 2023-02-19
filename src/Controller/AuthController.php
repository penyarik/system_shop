<?php

namespace App\Controller;

use App\CustomEntity\Locale;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthController extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    #[Route(path: '/login/{seller_id?}', name: 'app_login',  requirements: ['seller_id' => '[0-9]+'])]
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator, Request $request): Response
    {
        $this->addFlash('success', $translator->trans('Your email address has been verified.'));

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = $this->getUser();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'seller_id' => $this->userService->getSeller($request->attributes->get('seller_id'))->getId(),
            'is_logged' => !is_null($user),
            'is_admin' => !is_null($this->userService->isAdmin($user)),
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
