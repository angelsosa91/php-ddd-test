<?php

declare(strict_types=1);

use App\Application\DTO\Request\RegisterUserRequest;
use App\Application\EventHandler\UserRegisteredEventHandler;
use App\Application\UseCase\User\RegisterUserUseCase;
use App\Application\UseCase\User\ListUsersUseCase;
use App\Domain\Event\User\UserRegisteredEvent;
use App\Infrastructure\Controller\RegisterUserController;
use App\Infrastructure\Controller\ListUsersController;
use App\Infrastructure\EventDispatcher\CustomEventDispatcher;
use App\Infrastructure\Persistence\Doctrine\Mapper\UserMapper;
use App\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use Doctrine\ORM\EntityManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once __DIR__ . '/../vendor/autoload.php';

// Configurar respuesta como JSON
header('Content-Type: application/json');

// Obtener el EntityManager de Doctrine
/** @var EntityManager $entityManager */
$entityManager = require_once __DIR__ . '/../config/doctrine/bootstrap.php';

// Crear logger
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/app.log', Logger::DEBUG));

// Crear el event dispatcher
$eventDispatcher = new CustomEventDispatcher();

// Registrar el manejador de eventos
$userRegisteredHandler = new UserRegisteredEventHandler($logger);
$eventDispatcher->addListener(UserRegisteredEvent::class, $userRegisteredHandler);

// Crear dependencias
$userMapper = new UserMapper();
$userRepository = new DoctrineUserRepository($entityManager, $userMapper);

// Crear dependencias para registrar usuarios
$registerUserUseCase = new RegisterUserUseCase($userRepository, $eventDispatcher);
$registerUserController = new RegisterUserController($registerUserUseCase);

// Crear dependencias para listar usuarios
$listUsersUseCase = new ListUsersUseCase($userRepository);
$listUsersController = new ListUsersController($listUsersUseCase);

// Obtener la ruta desde la URL
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Obtener los datos de la solicitud
$requestData = json_decode(file_get_contents('php://input'), true) ?? [];

// Enrutamiento básico
if ($requestMethod === 'POST' && $requestUri === '/api/users') {
    $response = $registerUserController->execute($requestData);
    http_response_code($response['code']);
    echo json_encode($response);
    exit;
} elseif ($requestMethod === 'GET' && preg_match('/^\/api\/users(\/([a-zA-Z0-9-]+))?$/', $requestUri, $matches)) {
    // Extraer el ID de usuario si existe en la URL
    $userId = isset($matches[2]) ? $matches[2] : null;
    
    $response = $listUsersController->execute($userId);
    http_response_code($response['code']);
    echo json_encode($response);
    exit;
}
// Ruta no encontrada
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Ruta no encontrada',
    'code' => 404
]);