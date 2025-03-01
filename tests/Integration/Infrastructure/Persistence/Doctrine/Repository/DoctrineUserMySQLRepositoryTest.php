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
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

/**
 * Esta prueba se aplica con un servidor MySQL
 */
final class DoctrineUserMySQLRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private DoctrineUserRepository $repository;
    private UserMapper $mapper;

    protected function setUp(): void
    {
        // Skip if MySQL is not available
        if (!extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('MySQL extension is not available');
        }

        // Skip if MySQL connection details are not available
        $host = getenv('TEST_DB_HOST') ?: 'mysql';
        $port = getenv('TEST_DB_PORT') ?: '3306';
        $user = getenv('TEST_DB_USER') ?: 'app_user';
        $password = getenv('TEST_DB_PASSWORD') ?: 'app_password';
        $dbName = getenv('TEST_DB_NAME') ?: 'app_test_db';

        // Create configuration for test database
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../../../../../../src/Infrastructure/Persistence/Doctrine/Entity'],
            true
        );

        // Define connection parameters for MySQL test database
        $connectionParams = [
            'driver' => 'pdo_mysql',
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'password' => $password,
            'dbname' => $dbName,
            'charset' => 'utf8mb4',
        ];

        try {
            // Create EntityManager
            $connection = DriverManager::getConnection($connectionParams);
            $this->entityManager = new EntityManager($connection, $config);

            // Try to connect to ensure database exists
            $connection->connect();
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not connect to MySQL: ' . $e->getMessage());
            return;
        }

        // Create schema
        try {
            $schemaTool = new SchemaTool($this->entityManager);
            $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
            
            // Drop and recreate tables
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not create schema: ' . $e->getMessage());
            return;
        }

        // Create mapper and repository
        $this->mapper = new UserMapper();
        $this->repository = new DoctrineUserRepository($this->entityManager, $this->mapper);
    }

    /**
     * Test the complete CRUD cycle with MySQL
     */
    public function testFullCRUDCycle(): void
    {
        // 1. CREATE
        $userId = UserId::create();
        $name = Name::fromString('Test User');
        $email = Email::fromString('test@example.com');
        $password = Password::fromPlainPassword('Password123!');
        
        $user = User::register($userId, $name, $email, $password);
        
        // Save the user
        $this->repository->save($user);
        
        // 2. READ by ID
        $foundById = $this->repository->findById($userId);
        $this->assertNotNull($foundById);
        $this->assertTrue($userId->equals($foundById->id()));
        
        // 3. READ by Email
        $foundByEmail = $this->repository->findByEmail($email);
        $this->assertNotNull($foundByEmail);
        $this->assertTrue($email->equals($foundByEmail->email()));
        
        // 4. READ all
        $allUsers = $this->repository->findAll();
        $this->assertCount(1, $allUsers);
        
        // 5. DELETE
        $this->repository->delete($userId);
        
        // 6. Verify deletion
        $shouldBeNull = $this->repository->findById($userId);
        $this->assertNull($shouldBeNull);
        
        $allUsersAfterDelete = $this->repository->findAll();
        $this->assertCount(0, $allUsersAfterDelete);
    }

    /**
     * Clean up the database after tests
     */
    protected function tearDown(): void
    {
        if (isset($this->entityManager)) {
            // Drop all tables after tests
            try {
                $schemaTool = new SchemaTool($this->entityManager);
                $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
                $schemaTool->dropSchema($metadata);
            } catch (\Exception $e) {
                // Ignore errors during cleanup
            }

            // Close connection
            $this->entityManager->getConnection()->close();
        }
    }
}