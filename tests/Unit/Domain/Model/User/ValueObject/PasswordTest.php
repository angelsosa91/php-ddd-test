<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\User\ValueObject;

use App\Domain\Model\User\ValueObject\Password;
use App\Domain\Model\User\Exception\WeakPasswordException;
use PHPUnit\Framework\TestCase;

final class PasswordTest extends TestCase
{
    public function testFromPlainPasswordWithValidPassword(): void
    {
        $validPassword = 'Password123!';
        $password = Password::fromPlainPassword($validPassword);
        // Verificamos que el hash no sea el texto plano
        $this->assertNotSame($validPassword, $password->value());
        // Validamos que la verificación funcione
        $this->assertTrue($password->verify($validPassword));
    }
    
    public function testFromPlainPasswordWithTooShortPasswordThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        Password::fromPlainPassword('Pass1!');
    }
    
    public function testFromPlainPasswordWithoutUppercaseThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        Password::fromPlainPassword('password123!');
    }
    
    public function testFromPlainPasswordWithoutNumberThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        Password::fromPlainPassword('Password!');
    }
    
    public function testFromPlainPasswordWithoutSpecialCharThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        Password::fromPlainPassword('Password123');
    }
    
    public function testFromHash(): void
    {
        //texto plano
        $plainPassword = 'Password123!';
        $password1 = Password::fromPlainPassword($plainPassword);
        //hashed pass
        $hashedPassword = $password1->value();
        $password2 = Password::fromHash($hashedPassword);
        // El valor hasheado debe ser el mismo
        $this->assertSame($hashedPassword, $password2->value());
        $this->assertTrue($password2->verify($plainPassword));
    }
    
    /**
     * @dataProvider provideValidPasswords
     */
    public function testValidPasswordFormats(string $validPassword): void
    {
        $password = Password::fromPlainPassword($validPassword);
        $this->assertTrue($password->verify($validPassword));
    }
    
    public function provideValidPasswords(): array
    {
        return [
            'requerimientos minimos' => ['Password1!'],
            'longitud de password' => ['AValidPassword123!'],
            'con multiples caracteres especiales' => ['P@ssw0rd!#$'],
            'con multiples numeros' => ['Password123!'],
            'con multiples letras mayusculas' => ['PASSword1!'],
        ];
    }
}