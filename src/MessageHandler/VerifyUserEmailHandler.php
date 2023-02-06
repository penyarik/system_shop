<?php

namespace App\MessageHandler;

use App\Message\VerifyUserEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[AsMessageHandler]
class VerifyUserEmailHandler
{
    public function __construct(
        private readonly TransportInterface $mailer,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
    ) {

    }

    public function __invoke(VerifyUserEmail $verifyUserEmail): void
    {
        $user = $verifyUserEmail->getUser();

        $email = (new TemplatedEmail())
            ->from(new Address(getenv('SITE_EMAIL_ADDRESS'), getenv('SITE_NAME')))
            ->to($user->getEmail())
            ->subject('Please Confirm your Email')
            ->htmlTemplate('registration/confirmation_email.html.twig');

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }
}