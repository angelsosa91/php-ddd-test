<?php

declare(strict_types=1);

namespace App\Domain\Model\User\ValueObject;

use App\Domain\Model\User\Exception\WeakPasswordException;

final class Password
{
    private const MIN_LENGTH = 8;
    private const PATTERN_UPPERCASE = '/[A-Z]/';
    private const PATTERN_NUMBER = '/[0-9]/';
    private const PATTERN_SPECIAL = '/[^a-zA-Z0-9]/';

    private string $value;

    private function __construct(string $value, bool $isHashed = false)
    {
        if (!$isHashed) {
            $this->ensureIsValidPassword($value);
            $this->value = password_hash($value, PASSWORD_BCRYPT);
        } else {
            $this->value = $value;
        }
    }

    public static function fromPlainPassword(string $plainPassword): self
    {
        return new self($plainPassword);
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword, true);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->value);
    }

    private function ensureIsValidPassword(string $password): void
    {
        if (mb_strlen($password) < self::MIN_LENGTH) {
            throw new WeakPasswordException('El password debe contener al menos ' . self::MIN_LENGTH . ' caracteres');
        }

        if (!preg_match(self::PATTERN_UPPERCASE, $password)) {
            throw new WeakPasswordException('El password debe contener al menos letra mayúscula');
        }

        if (!preg_match(self::PATTERN_NUMBER, $password)) {
            throw new WeakPasswordException('El password debe contener al menos un número');
        }

        if (!preg_match(self::PATTERN_SPECIAL, $password)) {
            throw new WeakPasswordException('El password debe contener al menos un caracter especial');
        }
    }
}