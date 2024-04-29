<?php

namespace Frame\Storage;

interface TokenStorageInterface
{
    public function set(string $key, string $token): bool;

    public function get(string $key): ?string;
}