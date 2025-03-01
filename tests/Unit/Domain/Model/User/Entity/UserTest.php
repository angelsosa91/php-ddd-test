<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\User\Entity;

use App\Domain\Event\User\UserRegisteredEvent;
use App\Domain\Model\User\Entity\User;
use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\ValueObject\Name;
use App\Domain\Model\User\ValueObject\Password;
use App\Domain\Model\User\ValueObject\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testRegister(): void
    {
        // instancia
        $userId = UserId::create();
        $name = Name::fromString('John Doe');
        $email = Email::fromString('john@example.com');
        $password = Password::fromPlainPassword('Password123!');
        
        // registra
        $user = User::register($userId, $name, $email, $password);
        
        // assert
        $this->assertTrue($userId->equals($user->id()));
        $this->assertTrue($name->equals($user->name()));
        $this->assertTrue($email->equals($user->email()));
        $this->assertTrue($password->value() === $user->password()->value());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->createdAt());
        
        // validamos registro correcto
        $events = $user->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserRegisteredEvent::class, $events[0]);
        
        // validamos datos correcto
        $event = $events[0];
        $this->assertTrue($userId->equals($event->userId()));
        $this->assertTrue($email->equals($event->email()));
        $this->assertTrue($name->equals($event->name()));
    }
}