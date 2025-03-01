<?php

declare(strict_types=1);

namespace App\Application\DTO\Response;

use App\Domain\Model\User\Entity\User;
use DateTimeImmutable;

final class UserResponseDTO
{
    private string $id;
    private string $name;
    private string $email;
    private string $createdAt;

    private function __construct(
        string $id,
        string $name,
        string $email,
        string $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->id()->value(),
            $user->name()->value(),
            $user->email()->value(),
            $user->createdAt()->format('Y-m-d H:i:s')
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt
        ];
    }
}