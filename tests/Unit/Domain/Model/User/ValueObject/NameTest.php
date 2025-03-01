<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\User\ValueObject;

use App\Domain\Model\User\ValueObject\Name;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{
    public function testFromStringWithValidName(): void
    {
        $validName = 'John Doe';
        $name = Name::fromString($validName);
        $this->assertSame($validName, $name->value());
    }
    
    public function testFromStringWithNameTooShortThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);        
        Name::fromString('J');
    }
    
    public function testFromStringWithNameTooLongThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);        
        $longName = str_repeat('a', 101);
        Name::fromString($longName);
    }
    
    public function testFromStringWithInvalidCharactersThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);        
        Name::fromString('John Doe <script>alert("XSS")</script>');
    }
    
    public function testEqualsReturnsTrueForSameName(): void
    {
        $nameString = 'John Doe';
        $name1 = Name::fromString($nameString);
        $name2 = Name::fromString($nameString);        
        $this->assertTrue($name1->equals($name2));
    }
    
    public function testEqualsReturnsFalseForDifferentName(): void
    {
        $name1 = Name::fromString('John Doe');
        $name2 = Name::fromString('Jane Doe');        
        $this->assertFalse($name1->equals($name2));
    }
    
    public function testToStringReturnsNameString(): void
    {
        $nameString = 'John Doe';
        $name = Name::fromString($nameString);        
        $this->assertSame($nameString, (string) $name);
    }
    
    /**
     * @dataProvider provideValidNames
     */
    public function testValidNameFormats(string $validName): void
    {
        $name = Name::fromString($validName);
        $this->assertSame($validName, $name->value());
    }
    
    public function provideValidNames(): array
    {
        return [
            'simple' => ['John'],
            'con espacios' => ['John Doe'],
            'con guion medio' => ['John-Doe'],
            'con guion bajo' => ['John_Doe'],
            'con puntos' => ['John.Doe'],
            'con numeros' => ['John123'],
        ];
    }
}