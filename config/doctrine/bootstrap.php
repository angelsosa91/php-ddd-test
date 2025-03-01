<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

// Cargar variables de entorno desde .env si existe
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

// Configuración de Doctrine
$config = ORMSetup::createAttributeMetadataConfiguration(
    [__DIR__ . '/../../src/Infrastructure/Persistence/Doctrine/Entity'],
    $_ENV['APP_ENV'] === 'dev',
    __DIR__ . '/../../var/cache/doctrine/proxies',
    null
);

// Configuración de la conexión a la base de datos
$connectionParams = [
    'driver'   => 'pdo_mysql',
    'host'     => $_ENV['DB_HOST'] ?? 'mysql',
    'user'     => $_ENV['DB_USER'] ?? 'app_user',
    'password' => $_ENV['DB_PASSWORD'] ?? 'app_password',
    'dbname'   => $_ENV['DB_NAME'] ?? 'app_db',
    'charset'  => 'utf8mb4'
];

// Crear EntityManager
$connection = DriverManager::getConnection($connectionParams, $config);
$entityManager = new EntityManager($connection, $config);

return $entityManager;