<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\User\ValueObject;

use App\Domain\Model\User\ValueObject\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class UserIdTest extends TestCase
{
    public function testCreateGeneratesValidUuid(): void
    {
        $userId = UserId::create();
        
        $this->assertTrue(Uuid::isValid((string) $userId));
    }
    
    public function testFromStringWithValidUuid(): void
    {
        $validUuid = Uuid::uuid4()->toString();
        $userId = UserId::fromString($validUuid);
        
        $this->assertSame($validUuid, $userId->value());
    }
    
    public function testFromStringWithInvalidUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        UserId::fromString('invalid-uuid');
    }
    
    public function testEqualsReturnsTrueForSameId(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $userId1 = UserId::fromString($uuid);
        $userId2 = UserId::fromString($uuid);
        
        $this->assertTrue($userId1->equals($userId2));
    }
    
    public function testEqualsReturnsFalseForDifferentId(): void
    {
        $userId1 = UserId::create();
        $userId2 = UserId::create();
        
        $this->assertFalse($userId1->equals($userId2));
    }
    
    public function testToStringReturnsUuidString(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $userId = UserId::fromString($uuid);
        
        $this->assertSame($uuid, (string) $userId);
    }
}