<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Domain\Event\User\UserRegisteredEvent;
use Psr\Log\LoggerInterface;

final class UserRegisteredEventHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        // En un escenario real, aquí enviaríamos un email de bienvenida
        // Por ahora, solo registramos un mensaje en el log
        $this->logger->info(
            'Usuario registrado correctamente: {email}. Se debe enviar un email de bienvenida.',
            ['email' => $event->email()->value()]
        );
        
        // Simulación del envío de email de bienvenida
        $this->sendWelcomeEmail($event);
    }
    
    private function sendWelcomeEmail(UserRegisteredEvent $event): void
    {
        // En un escenario real, aquí enviaríamos un email real
        // Esta es una simulación para fines de la prueba
        $this->logger->info(
            'Email de bienvenida enviado a {email}',
            ['email' => $event->email()->value()]
        );
    }
}