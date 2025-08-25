<?php

namespace App\EventListener;

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
    public const MANAGED_LOCALE = ['en', 'fr'];

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $preferredLocale = $request->getPreferredLanguage(self::MANAGED_LOCALE);

        if ($preferredLocale) {
            $request->setLocale($preferredLocale);
        }
    }
}
