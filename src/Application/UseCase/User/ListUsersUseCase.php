<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Application\DTO\Response\ListUsersResponseDTO;
use App\Application\DTO\Response\UserResponseDTO;
use App\Domain\Model\User\ValueObject\UserId;
use App\Domain\Repository\UserRepositoryInterface;

final class ListUsersUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(?string $userId = null): ListUsersResponseDTO
    {
        if ($userId !== null) {
            // Buscar un usuario específico por ID
            $user = $this->userRepository->findById(UserId::fromString($userId));
            
            if ($user === null) {
                return ListUsersResponseDTO::fromUserResponses([]);
            }
            
            return ListUsersResponseDTO::fromUserResponses([
                UserResponseDTO::fromUser($user)
            ]);
        }
        
        // Buscar todos los usuarios
        $users = $this->userRepository->findAll();
        
        $userResponses = array_map(
            fn($user) => UserResponseDTO::fromUser($user),
            $users
        );
        
        return ListUsersResponseDTO::fromUserResponses($userResponses);
    }
}