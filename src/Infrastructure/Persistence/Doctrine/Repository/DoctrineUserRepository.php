<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\Entity\User;
use App\Domain\Model\User\ValueObject\UserId;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use App\Infrastructure\Persistence\Doctrine\Mapper\UserMapper;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserMapper $mapper;

    public function __construct(EntityManagerInterface $entityManager, UserMapper $mapper)
    {
        $this->entityManager = $entityManager;
        $this->mapper = $mapper;
    }

    public function save(User $user): void
    {
        $doctrineUser = $this->mapper->toDoctrine($user);
        
        $this->entityManager->persist($doctrineUser);
        $this->entityManager->flush();
    }

    public function findById(UserId $id): ?User
    {
        $doctrineUser = $this->entityManager->find(DoctrineUser::class, $id->value());
        
        if ($doctrineUser === null) {
            return null;
        }
        
        return $this->mapper->toDomain($doctrineUser);
    }

    public function findByEmail(Email $email): ?User
    {
        $repository = $this->entityManager->getRepository(DoctrineUser::class);
        $doctrineUser = $repository->findOneBy(['email' => $email->value()]);
        
        if ($doctrineUser === null) {
            return null;
        }
        
        return $this->mapper->toDomain($doctrineUser);
    }

    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(DoctrineUser::class);
        $doctrineUsers = $repository->findAll();
        
        $users = [];
        foreach ($doctrineUsers as $doctrineUser) {
            $users[] = $this->mapper->toDomain($doctrineUser);
        }
        
        return $users;
    }

    public function delete(UserId $id): void
    {
        $doctrineUser = $this->entityManager->find(DoctrineUser::class, $id->value());
        
        if ($doctrineUser !== null) {
            $this->entityManager->remove($doctrineUser);
            $this->entityManager->flush();
        }
    }
}