<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Application\DTO\Request\RegisterUserRequest;
use App\Application\DTO\Response\UserResponseDTO;
use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\Exception\UserAlreadyExistsException;
use App\Domain\Model\User\ValueObject\Name;
use App\Domain\Model\User\ValueObject\Password;
use App\Domain\Model\User\Entity\User;
use App\Domain\Model\User\ValueObject\UserId;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\EventDispatcher\EventDispatcherInterface;

final class RegisterUserUseCase
{
    private UserRepositoryInterface $userRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(RegisterUserRequest $request): UserResponseDTO
    {
        $email = Email::fromString($request->email());
        
        // Verificar si el email ya está en uso
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser !== null) {
            throw new UserAlreadyExistsException($email->value());
        }
        
        // Crear el usuario
        $user = User::register(
            UserId::create(),
            Name::fromString($request->name()),
            $email,
            Password::fromPlainPassword($request->password())
        );
        
        // Guardar el usuario
        $this->userRepository->save($user);
        
        // Disparar eventos de dominio
        foreach ($user->pullEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
        
        return UserResponseDTO::fromUser($user);
    }
}