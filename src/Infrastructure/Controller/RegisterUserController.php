<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\DTO\Request\RegisterUserRequest;
use App\Application\UseCase\User\RegisterUserUseCase;
use App\Domain\Model\User\Exception\InvalidNameException;
use App\Domain\Model\User\Exception\InvalidEmailException;
use App\Domain\Model\User\Exception\UserAlreadyExistsException;
use App\Domain\Model\User\Exception\WeakPasswordException;

final class RegisterUserController
{
    private RegisterUserUseCase $registerUserUseCase;

    public function __construct(RegisterUserUseCase $registerUserUseCase)
    {
        $this->registerUserUseCase = $registerUserUseCase;
    }

    public function execute(array $requestData): array
    {
        try {
            // Validar datos de entrada
            if (!isset($requestData['name']) || !isset($requestData['email']) || !isset($requestData['password'])) {
                return [
                    'success' => false,
                    'message' => 'Campos obligatorios faltantes',
                    'code' => 400
                ];
            }

            // Crear el DTO de solicitud
            $request = new RegisterUserRequest(
                $requestData['name'],
                $requestData['email'],
                $requestData['password']
            );

            // Ejecutar el caso de uso
            $response = $this->registerUserUseCase->execute($request);

            // Devolver respuesta exitosa
            return [
                'success' => true,
                'data' => $response->toArray(),
                'code' => 201
            ];
        } catch (InvalidNameException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400
            ];
        } catch (InvalidEmailException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400
            ];
        } catch (WeakPasswordException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400
            ];
        } catch (UserAlreadyExistsException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 409
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