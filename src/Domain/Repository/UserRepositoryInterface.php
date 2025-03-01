<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\Entity\User;
use App\Domain\Model\User\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    
    public function findById(UserId $id): ?User;
    
    public function findByEmail(Email $email): ?User;

    public function findAll(): array;
    
    public function delete(UserId $id): void;
}