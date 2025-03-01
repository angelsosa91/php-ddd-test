<?php

declare(strict_types=1);

namespace App\Domain\Model\User\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class UserId
{
    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        $this->value = $value;
    }

    public static function create(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function ensureIsValidUuid(string $id): void
    {
        if (!Uuid::isValid($id)) {
            throw new InvalidArgumentException(
                sprintf('<%s> no permite el valor <%s>.', static::class, $id)
            );
        }
    }
}