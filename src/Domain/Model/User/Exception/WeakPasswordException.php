<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Exception;

use InvalidArgumentException;

final class WeakPasswordException extends InvalidArgumentException
{
    public function __construct(string $message = 'El password no cumple los requisitos de seguridad')
    {
        parent::__construct($message);
    }
}