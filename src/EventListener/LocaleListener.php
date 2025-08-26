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
#[AsEventListener(method: 'setFromAcceptLanguage', priority: 32)]
#[AsEventListener(method: 'setFromSession', priority: 31)]
final readonly class LocaleListener
{
    public function __construct(private array $managedLocales)
    {
    }

    public function setFromAcceptLanguage(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $preferredLocale = $request->getPreferredLanguage($this->managedLocales);

        if ($preferredLocale) {
            $request->setLocale($preferredLocale);
        }
    }

    /*
     * Ici, il serait aussi simple et plus efficace d'avoir les deux traitements dans une seule méthode.
     */
    public function setFromSession(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $sessionLocale = $request->getSession()->get('locale');

        if ($sessionLocale) {
            $request->setLocale($sessionLocale);
        }
    }
}
