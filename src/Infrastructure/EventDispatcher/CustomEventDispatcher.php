<?php

declare(strict_types=1);

namespace App\Infrastructure\EventDispatcher;

final class CustomEventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];

    public function dispatch(object $event): void
    {
        $eventName = get_class($event);
        
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        
        foreach ($this->listeners[$eventName] as $listener) {
            $listener($event);
        }
    }

    public function addListener(string $eventName, callable $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        
        $this->listeners[$eventName][] = $listener;
    }
}