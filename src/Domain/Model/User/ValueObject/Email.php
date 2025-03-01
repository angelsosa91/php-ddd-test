<?php

declare(strict_types=1);

namespace App\Domain\Model\User\ValueObject;

use App\Domain\Model\User\Exception\InvalidEmailException;

final class Email
{
    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidEmail($value);
        $this->value = $value;
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function ensureIsValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
    }
}