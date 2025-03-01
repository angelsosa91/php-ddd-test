<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Exception;

use DomainException;

final class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('El usuario que intenta registrar con el email <%s> ya existe', $email));
    }
}