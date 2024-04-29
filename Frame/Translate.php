<?php declare(strict_types=1);

namespace Frame;

class Translate
{
    private array $translations;

    public function __construct(string $filePath)
    {
        $this->translations = [];

        if (file_exists($filePath)) {
            $this->translations = include $filePath;
        }
    }

    public function get(string $key, array $placeholders = []): string
    {
        if ($message = $this->getMessage($key)) {
            return empty($placeholders) ? $message : $this->replacePlaceholders($message, $placeholders);
        }
        return $this->noMessageFormat($key);
    }

    private function getMessage(string $key): string|null
    {
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }
        return null;
    }

    private function replacePlaceholders(string $message, array $placeholders): string
    {
        foreach ($placeholders as $placeholder => $value) {
            $message = str_replace('{' . $placeholder . '}', $value, $message);
        }
        return $message;
    }

    private function noMessageFormat(string $key): string
    {
        return '{$lang->' . $key . '}';
    }

    public function __get(string $key): string
    {
        if ($translation = $this->getMessage($key)) {
            return $translation;
        }
        return $this->noMessageFormat($key);
    }
}