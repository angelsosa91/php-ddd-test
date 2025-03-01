<?php

declare(strict_types=1);

namespace App\Domain\Model\User\ValueObject;

use App\Domain\Model\User\Exception\InvalidNameException;

final class Name
{
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 100;
    private const PATTERN = '/^[a-zA-Z0-9\s\-_.]+$/';

    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidName($value);
        $this->value = $value;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Name $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function ensureIsValidName(string $name): void
    {
        $trimmedName = trim($name);
        $length = mb_strlen($trimmedName);
        
        if ($length < self::MIN_LENGTH) {
            throw new InvalidNameException(
                sprintf('El nombre debe contener al menos %d caracteres', self::MIN_LENGTH)
            );
        }
        
        if ($length > self::MAX_LENGTH) {
            throw new InvalidNameException(
                sprintf('El nombre no debe superar los %d caracteres', self::MAX_LENGTH)
            );
        }
        
        if (!preg_match(self::PATTERN, $trimmedName)) {
            throw new InvalidNameException(
                'El nombre contiene caracteres inválidos. Solo se permiten letras, números, espacios, guiones, guiones bajos y puntos.'
            );
        }
    }
}