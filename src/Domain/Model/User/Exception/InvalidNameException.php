<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Exception;

use InvalidArgumentException;

final class InvalidNameException extends InvalidArgumentException
{
    public function __construct(string $message = 'El nombre es inválido')
    {
        parent::__construct($message);
    }
}