<?php

namespace App\EventListener;

use App\Constants;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * LocaleListener configure définitivement la locale avec priorité 16
 * LocaleAwareListener applique cette config aux différents services utilisant la locale priorité 15
 *
 * → Notre priorité doit donc être strictement supérieur à 16
 */
#[AsEventListener(priority: 32)]
final class AcceptLanguageListener
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $preferredLocale = $request->getPreferredLanguage(Constants::MANAGED_LOCALE);

        if ($preferredLocale) {
            $request->setLocale($preferredLocale);
        }
    }
}
