<?php declare(strict_types=1);

namespace Frame;

class Messages
{
    private array $storage = [];
    private string $storageKey = 'flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->storage = &$_SESSION;
        $this->clearAll();
    }

    public function get(string $key): array|null
    {
        $message = $this->storage[$this->storageKey][$key] ?? null;
        $this->clear($key);
        return $message;
    }

    public function getAll(): array
    {
        $messages = $this->storage[$this->storageKey];
        $this->clearAll();
        return $messages;
    }

    public function addMany(string $key, array $messages): void
    {
        $this->storage[$this->storageKey][$key] = $messages;
    }

    public function add(string $key, string $message): void
    {
        $this->storage[$this->storageKey][$key][] = $message;
    }

    public function has(string $key): bool
    {
        return isset($this->storage[$this->storageKey][$key]);
    }

    public function clear(string $key): void
    {
        unset($this->storage[$this->storageKey][$key]);
    }

    public function clearAll(): void
    {
        $this->storage[$this->storageKey] = [];
    }
}