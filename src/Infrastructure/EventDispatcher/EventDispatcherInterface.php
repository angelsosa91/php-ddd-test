<?php

declare(strict_types=1);

namespace App\Infrastructure\EventDispatcher;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
    
    public function addListener(string $eventName, callable $listener): void;
}