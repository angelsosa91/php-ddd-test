<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\UseCase\User;

use App\Application\DTO\Request\RegisterUserRequest;
use App\Application\DTO\Response\UserResponseDTO;
use App\Application\UseCase\User\RegisterUserUseCase;
use App\Domain\Event\User\UserRegisteredEvent;
use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\Exception\UserAlreadyExistsException;
use App\Domain\Model\User\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class RegisterUserUseCaseTest extends MockeryTestCase
{
    private UserRepositoryInterface $userRepository;
    private EventDispatcherInterface $eventDispatcher;
    private RegisterUserUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->useCase = new RegisterUserUseCase($this->userRepository, $this->eventDispatcher);
    }

    public function testExecuteRegistersUserSuccessfully(): void
    {
        // Arrange
        $name = 'John Doe';
        $email = 'john@example.com';
        $password = 'Password123!';
        
        $request = new RegisterUserRequest($name, $email, $password);
        
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with(Mockery::type(Email::class))
            ->andReturn(null);
        
        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(User::class));
        
        $this->eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(UserRegisteredEvent::class));
        
        // Act
        $response = $this->useCase->execute($request);
        
        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertNotEmpty($response->id());
        $this->assertEquals($name, $response->name());
        $this->assertEquals($email, $response->email());
    }
    
    public function testExecuteThrowsExceptionWhenEmailAlreadyExists(): void
    {
        // Arrange
        $name = 'John Doe';
        $email = 'john@example.com';
        $password = 'Password123!';
        
        $request = new RegisterUserRequest($name, $email, $password);
        
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with(Mockery::type(Email::class))
            ->andReturn(Mockery::mock(User::class));
        
        // Assert
        $this->expectException(UserAlreadyExistsException::class);
        
        // Act
        $this->useCase->execute($request);
    }
}