<?php
namespace App\Subscribers;

use App\CustomEntity\Locale;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $session->start();

        if (!$request->getSession()->get('_locale')) {

            if ($email = $session->get('_security.last_username')) {
                $locale = strtolower(Locale::tryFrom($this->userRepository->findOneByField($email, 'email')->getLocaleId())?->name);
            }

            $request->getSession()->set('_locale', $locale ?? $request->server->get('SITE_DEFAULT_LOCALE'));
        }

        $request->setLocale($request->getSession()->get('_locale', $request->server->get('SITE_DEFAULT_LOCALE')));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 17]]
        ];
    }
}
