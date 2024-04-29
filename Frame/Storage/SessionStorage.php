<?php

namespace Frame\Storage;

class SessionStorage implements TokenStorageInterface
{
    public function set(string $key, string $token): bool
    {
        $_SESSION[$key] = $token;

        return true;
    }

    public function get(string $key): ?string
    {
        return empty($_SESSION[$key])
            ? null
            : (string)$_SESSION[$key];
    }
}