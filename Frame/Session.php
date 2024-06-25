<?php declare(strict_types=1);

namespace Frame;

class Session
{
    private array $session = [];

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->session = &$_SESSION;
    }

    public function get(string $key, mixed $default = null): ?string
    {
        return $this->session[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->session;
    }

    public function set(string $key, mixed $value): void
    {
        $this->session[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->session);
    }

    public function unset(string $key): void
    {
        unset($this->session[$key]);
    }

    public function clear(): void
    {
        $this->session = [];
    }

    /** Regenerate current session id
     * Useful to protect against session spoofing
     * It is necessary to regenerate the identifier in the following cases:
     * - at the time of creating a new session
     * - at the time of change, user authorization
     */
    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }
}