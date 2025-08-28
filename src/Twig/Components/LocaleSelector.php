<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class LocaleSelector
{
    public string $buttonClass = 'btn-primary';

    public function __construct(
        public array $managedLocales,
    ) {
    }
}
