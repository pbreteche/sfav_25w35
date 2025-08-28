<?php

namespace App\Form\TypeGuesser;

use App\DateRange;
use App\Form\DateRangeType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess;

class TypeGuesser implements FormTypeGuesserInterface
{

    /**
     * @inheritDoc
     */
    public function guessType(string $class, string $property): ?Guess\TypeGuess
    {
        $reflectionProperty = new \ReflectionProperty($class, $property);
        $type = $reflectionProperty->getType();
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        if (DateRange::class === $type->getName()) {
            return new Guess\TypeGuess(DateRangeType::class, [], Guess\Guess::VERY_HIGH_CONFIDENCE);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function guessRequired(string $class, string $property): ?Guess\ValueGuess
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function guessMaxLength(string $class, string $property): ?Guess\ValueGuess
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function guessPattern(string $class, string $property): ?Guess\ValueGuess
    {
        return null;
    }
}
