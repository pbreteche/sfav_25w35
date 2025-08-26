<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ExampleEvent extends Event
{
    public function __construct(
        private readonly string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
