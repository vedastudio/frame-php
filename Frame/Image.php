<?php

namespace Frame;

use Psr\Http\Message\UploadedFileInterface;

class Image
{
    private array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

    public function upload(UploadedFileInterface $uploadedFile, string $uploadPath): string|false
    {
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($uploadedFile->getError() === UPLOAD_ERR_OK && $this->isImage($uploadedFile)) {
            $fileName = $this->generateFileName($uploadedFile);
            $uploadedFile->moveTo($uploadPath . DIRECTORY_SEPARATOR . $fileName);
            return $fileName;
        }

        return false;
    }

    public function unlink(string $filename, string $folderPath): void
    {
        unlink($folderPath . DIRECTORY_SEPARATOR . $filename);
    }

    private function isImage(UploadedFileInterface $file): bool
    {
        $mime = $file->getClientMediaType();
        return in_array($mime, $this->allowedMimeTypes);
    }

    protected function generateFileName(UploadedFileInterface $file): string
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        return sprintf('%s.%0.8s', $basename, $extension);
    }

}