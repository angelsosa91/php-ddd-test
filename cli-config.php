<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// Carga el EntityManager desde el archivo de configuración de Doctrine
$entityManager = require_once __DIR__ . '/config/doctrine/bootstrap.php';

// Crear el SingleManagerProvider para Doctrine 3
$entityManagerProvider = new SingleManagerProvider($entityManager);

// Retornar el HelperSet como se requiere en la nueva versión de Doctrine
return ConsoleRunner::createHelperSet($entityManager);