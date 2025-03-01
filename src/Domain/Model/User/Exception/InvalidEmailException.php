<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Exception;

use InvalidArgumentException;

final class InvalidEmailException extends InvalidArgumentException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('El email <%s> es inválido', $email));
    }
}