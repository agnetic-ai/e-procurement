<?php

class FileUploadHelper
{
    public static function upload(
        array $file,
        string $targetDir,
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'],
        int $maxSize = 2097152
    ): string {

        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed.');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception(
                'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)
            );
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('File size exceeds 2MB limit.');
        }

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0775, true)) {
                throw new Exception('Failed to create upload directory.');
            }
        }

        $newFileName = uniqid('pay_', true) . '.' . $extension;
        $targetPath  = rtrim($targetDir, '/') . '/' . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file.');
        }

        return $targetPath;
    }
}
