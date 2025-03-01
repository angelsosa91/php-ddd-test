<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\ValueObject\Name;
use App\Domain\Model\User\ValueObject\Password;
use App\Domain\Model\User\Entity\User;
use App\Domain\Model\User\ValueObject\UserId;
use App\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;

final class UserMapper
{
    public function toDomain(DoctrineUser $doctrineUser): User
    {
        // Usamos una reflection para crear un User sin usar el constructor público
        // ya que debemos reconstruir un User existente
        $reflection = new \ReflectionClass(User::class);
        $user = $reflection->newInstanceWithoutConstructor();
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($user, UserId::fromString($doctrineUser->getId()));
        
        $nameProperty = $reflection->getProperty('name');
        $nameProperty->setAccessible(true);
        $nameProperty->setValue($user, Name::fromString($doctrineUser->getName()));
        
        $emailProperty = $reflection->getProperty('email');
        $emailProperty->setAccessible(true);
        $emailProperty->setValue($user, Email::fromString($doctrineUser->getEmail()));
        
        $passwordProperty = $reflection->getProperty('password');
        $passwordProperty->setAccessible(true);
        $passwordProperty->setValue($user, Password::fromHash($doctrineUser->getPassword()));
        
        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($user, $doctrineUser->getCreatedAt());
        
        return $user;
    }

    public function toDoctrine(User $user): DoctrineUser
    {
        return new DoctrineUser(
            $user->id()->value(),
            $user->name()->value(),
            $user->email()->value(),
            $user->password()->value(),
            $user->createdAt()
        );
    }
}