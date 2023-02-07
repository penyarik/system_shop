<?php
namespace App\Subscribers;

use App\Repository\LocaleRepository;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class LocaleSubscriber implements EventSubscriberInterface
{
    private string $defautlLocale;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LocaleRepository $localeRepository,
    ) {
        $this->defautlLocale = getenv('SITE_EMAIL_ADDRESS');
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $session->start();

        if (!$request->getSession()->get('_locale')) {

            if ($email = $session->get('_security.last_username')) {
                $localeId = $this->userRepository->findOneByField($email, 'email')->getLocaleId();
                $localeName = $localeId ? $this->localeRepository->find($localeId)?->getName() : null;
            }

            $request->getSession()->set('_locale', $localeName ?? $this->defautlLocale);
        }

        $request->setLocale($request->getSession()->get('_locale', $this->defautlLocale));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 17]]
        ];
    }
}
