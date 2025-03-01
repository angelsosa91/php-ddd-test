<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\User\ValueObject;

use App\Domain\Model\User\ValueObject\Email;
use App\Domain\Model\User\Exception\InvalidEmailException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testFromStringWithValidEmail(): void
    {
        $validEmail = 'test@example.com';
        $email = Email::fromString($validEmail);
        
        $this->assertSame($validEmail, $email->value());
    }
    
    public function testFromStringWithInvalidEmailThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        Email::fromString('invalid-email');
    }
    
    public function testEqualsReturnsTrueForSameEmail(): void
    {
        $emailString = 'test@example.com';
        $email1 = Email::fromString($emailString);
        $email2 = Email::fromString($emailString);
        
        $this->assertTrue($email1->equals($email2));
    }
    
    public function testEqualsReturnsFalseForDifferentEmail(): void
    {
        $email1 = Email::fromString('test1@example.com');
        $email2 = Email::fromString('test2@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }
    
    public function testToStringReturnsEmailString(): void
    {
        $emailString = 'test@example.com';
        $email = Email::fromString($emailString);
        
        $this->assertSame($emailString, (string) $email);
    }
    
    /**
     * @dataProvider provideDifferentInvalidEmails
     */
    public function testDifferentInvalidEmailsThrowException(string $invalidEmail): void
    {
        $this->expectException(InvalidEmailException::class);
        
        Email::fromString($invalidEmail);
    }
    
    public function provideDifferentInvalidEmails(): array
    {
        return [
            'vacio' => [''],
            'sin dominio' => ['test@'],
            'sin usuario' => ['@example.com'],
            'sin @' => ['testexample.com'],
            'doble @' => ['test@@example.com'],
            'con espacios' => ['test @example.com'],
        ];
    }
}