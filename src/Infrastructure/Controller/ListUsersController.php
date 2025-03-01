<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\UseCase\User\ListUsersUseCase;
use App\Domain\Model\User\ValueObject\UserId;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class ListUsersController
{
    private ListUsersUseCase $listUsersUseCase;

    public function __construct(ListUsersUseCase $listUsersUseCase)
    {
        $this->listUsersUseCase = $listUsersUseCase;
    }

    public function execute(?string $userId = null): array
    {
        try {
            // Validar UUID si se proporciona
            if ($userId !== null && !Uuid::isValid($userId)) {
                return [
                    'success' => false,
                    'message' => 'ID de usuario inválido',
                    'code' => 400
                ];
            }

            // Ejecutar el caso de uso
            $response = $this->listUsersUseCase->execute($userId);

            // Devolver respuesta exitosa
            return [
                'success' => true,
                'data' => $response->toArray(),
                'code' => 200
            ];
        } catch (InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400
            ];
        } catch (\Exception $e) {           
            return [
                'success' => false,
                'message' => 'Ha ocurrido un error interno. Por favor, inténtelo de nuevo más tarde.',
                'code' => 500
            ];
        }
    }
}