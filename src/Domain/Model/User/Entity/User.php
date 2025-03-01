<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Entity;

use App\Domain\Event\User\UserRegisteredEvent;
use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\ValueObject\Name;
use App\Domain\Model\User\ValueObject\Password;
use App\Domain\Model\User\ValueObject\UserId;
use DateTimeImmutable;

class User
{
    private UserId $id;
    private Name $name;
    private Email $email;
    private Password $password;
    private DateTimeImmutable $createdAt;
    private array $events = [];

    private function __construct(
        UserId $id,
        Name $name,
        Email $email,
        Password $password,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    public static function register(
        UserId $id,
        Name $name,
        Email $email,
        Password $password
    ): self {
        $user = new self(
            $id,
            $name,
            $email,
            $password,
            new DateTimeImmutable()
        );

        $user->recordEvent(new UserRegisteredEvent($id, $email, $name));

        return $user;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }

    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        
        return $events;
    }
}