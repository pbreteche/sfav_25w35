<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class LocaleSelector
{
    public function __construct(
        public array $managedLocales,
    ) {
    }
}
