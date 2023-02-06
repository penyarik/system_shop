<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Message\VerifyUserEmail;
use App\Repository\UserRepository;
use App\Security\AuthAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\Acl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    public function __construct(
        private readonly EmailVerifier              $emailVerifier,
        private readonly AuthAuthenticator          $authAuthenticator,
        private readonly UserAuthenticatorInterface $authenticatorManager
    )
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        MessageBusInterface         $bus,
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles([Acl::ROLE_USER->name]);

            $entityManager->persist($user);
            $entityManager->flush();

            $bus->dispatch(new VerifyUserEmail($user));

            $this->authenticatorManager->authenticateUser(
                $user,
                $this->authAuthenticator,
                new Request(request: [
                    'email' => $form->get('email')->getData(),
                    'password' => $form->get('plainPassword')->getData(),
                    '_csrf_token' => ''
                ])
            );

            return $this->redirectToRoute($this->authAuthenticator->getSuccessLoginRouteRedirect($user->getEmail()));
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', $translator->trans('Your email address has been verified.'));

        return $this->redirectToRoute($this->authAuthenticator->getSuccessLoginRouteRedirect($user->getEmail()));
    }
}
