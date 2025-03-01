<?php

declare(strict_types=1);

namespace App\Application\DTO\Response;

final class ListUsersResponseDTO
{
    private array $users;

    private function __construct(array $users)
    {
        $this->users = $users;
    }

    public static function fromUserResponses(array $userResponses): self
    {
        return new self($userResponses);
    }

    public function users(): array
    {
        return $this->users;
    }

    public function toArray(): array
    {
        return [
            'users' => array_map(fn($user) => $user->toArray(), $this->users)
        ];
    }
}