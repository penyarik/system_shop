<?php
namespace App\Subscribers;

use App\CustomEntity\Currency;
use App\CustomEntity\Locale;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Intl\Countries;


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

        if (!$countryCode = $request->getSession()->get('_country')) {
            $request->getSession()->set('_country',  $countryCode = $this->getCountryCode() ?? 'en');
        }

        if (!$request->getSession()->get('_currency')) {
            if ($email = $session->get('_security.last_username')) {
                $currency = strtolower(Currency::tryFrom($this->userRepository->findOneByField($email, 'email')->getCurrency())?->name);
            }

            $currency = $currency ?? $this->getCurrencyByCountry($countryCode);
            $request->getSession()->set('_currency', $currency);
        }

        if ($countryCode !== 'ru' && $countryCode !== 'ua') {
            $countryCode = 'en';
        }

        if (!$locale = $request->getSession()->get('_locale')) {

            if ($email = $session->get('_security.last_username')) {
                $locale = strtolower(Locale::tryFrom($this->userRepository->findOneByField($email, 'email')->getLocaleId())?->name);
            }

            $locale = $locale ?? $countryCode ?? $request->server->get('SITE_DEFAULT_LOCALE');

            $request->getSession()->set('_locale', $locale);
        }

        $request->setLocale( $locale);
    }

    private function getCountryCode(): ?string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $cip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $cip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $cip = $_SERVER['REMOTE_ADDR'];
        }

        try {
            return strtolower(json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip="'.$cip.'"'))->geoplugin_countryCode);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function getCurrencyByCountry(string $countryCode): string
    {
        switch ($countryCode) {
            case 'gb':
                return Currency::GBP->name;
            case 'ru':
            case 'ua':
                return Currency::UAH->name;
            case 'at':
            case 'pt':
            case 'ax':
            case 'al':
            case 'ad':
            case 'be':
            case 'bg':
            case 'gp':
            case 'de':
            case 'nl':
            case 'gr':
            case 'ep':
            case 'va':
            case 'ie':
            case 'es':
            case 'it':
            case 'cy':
            case 'kv':
            case 'lv':
            case 'lt':
            case 'lu':
                return Currency::EUR->name;
            default :
                return Currency::USD->name;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 17]]
        ];
    }
}
