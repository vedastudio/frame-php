<?php

namespace Frame;

class FileStorage
{
    private string $storageFolder;

    public function __construct(string $storageFolder = '')
    {
        $this->storageFolder = $storageFolder;
    }

    public function set(string $key, string $token): bool
    {
        $tokenFile = fopen($this->storageFolder . $key, 'wb');
        fwrite($tokenFile, $token);

        return fclose($tokenFile);
    }

    public function get(string $key): ?string
    {
        $filePath = $this->storageFolder . $key;

        return file_exists($filePath) ? file_get_contents($filePath) : null;
    }

    public function delete(string $key): bool
    {
        $filePath = $this->storageFolder . $key;

        return file_exists($filePath) && unlink($filePath);
    }
}