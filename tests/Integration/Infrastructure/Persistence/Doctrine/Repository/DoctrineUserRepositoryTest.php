<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\ValueObject\Name;
use App\Domain\Model\User\ValueObject\Password;
use App\Domain\Model\User\Entity\User;
use App\Domain\Model\User\ValueObject\UserId;
use App\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use App\Infrastructure\Persistence\Doctrine\Mapper\UserMapper;
use App\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;

final class DoctrineUserRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private DoctrineUserRepository $repository;
    private UserMapper $mapper;

    protected function setUp(): void
    {
        // Crear configuración para Doctrine en memoria
        $config = Setup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../../../../../../src/Infrastructure/Persistence/Doctrine/Entity'],
            true
        );

        // Crear conexión en memoria con SQLite
        $connection = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        // Crear EntityManager
        $this->entityManager = EntityManager::create($connection, $config);

        // Crear esquema de base de datos
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($metadata);

        // Crear el mapper y el repositorio
        $this->mapper = new UserMapper();
        $this->repository = new DoctrineUserRepository($this->entityManager, $this->mapper);
    }

    public function testSaveAndFindById(): void
    {
        // Arrange
        $userId = UserId::create();
        $name = Name::fromString('John Doe');
        $email = Email::fromString('john@example.com');
        $password = Password::fromPlainPassword('Password123!');
        
        $user = User::register($userId, $name, $email, $password);
        
        // Act
        $this->repository->save($user);
        $foundUser = $this->repository->findById($userId);
        
        // Assert
        $this->assertNotNull($foundUser);
        $this->assertTrue($foundUser->id()->equals($userId));
        $this->assertTrue($foundUser->name()->equals($name));
        $this->assertTrue($foundUser->email()->equals($email));
    }
    
    public function testFindByEmail(): void
    {
        // Arrange
        $userId = UserId::create();
        $name = Name::fromString('Jane Doe');
        $email = Email::fromString('jane@example.com');
        $password = Password::fromPlainPassword('Password123!');
        
        $user = User::register($userId, $name, $email, $password);
        
        // Act
        $this->repository->save($user);
        $foundUser = $this->repository->findByEmail($email);
        
        // Assert
        $this->assertNotNull($foundUser);
        $this->assertTrue($foundUser->id()->equals($userId));
        $this->assertTrue($foundUser->email()->equals($email));
    }
    
    public function testDelete(): void
    {
        // Arrange
        $userId = UserId::create();
        $name = Name::fromString('Alice Smith');
        $email = Email::fromString('alice@example.com');
        $password = Password::fromPlainPassword('Password123!');
        
        $user = User::register($userId, $name, $email, $password);
        $this->repository->save($user);
        
        // Act
        $this->repository->delete($userId);
        $foundUser = $this->repository->findById($userId);
        
        // Assert
        $this->assertNull($foundUser);
    }
}