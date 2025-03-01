<?php

declare(strict_types=1);

namespace App\Domain\Event\User;

use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\ValueObject\Name;
use App\Domain\Model\User\ValueObject\UserId;
use DateTimeImmutable;

final class UserRegisteredEvent
{
    private UserId $userId;
    private Email $email;
    private Name $name;
    private DateTimeImmutable $occurredOn;

    public function __construct(UserId $userId, Email $email, Name $name)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->name = $name;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}