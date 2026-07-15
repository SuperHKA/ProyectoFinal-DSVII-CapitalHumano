<?php

declare(strict_types=1);

namespace App\Servicios;

use finfo;
use RuntimeException;

/**
 * Valida el contenido real, normaliza extensiones y crea miniaturas seguras.
 */
final class FileUploadService
{
    private const IMAGE_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    private const MAX_SIZE = 2 * 1024 * 1024;
    private const MAX_IMAGE_PIXELS = 40_000_000;
    private const ALLOWED_IMAGE_FOLDERS = ['fotos'];

    public function uploadImage(array $file, string $subfolder = 'fotos'): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (!in_array($subfolder, self::ALLOWED_IMAGE_FOLDERS, true)) {
            throw new RuntimeException('La carpeta de imagen solicitada no esta permitida.');
        }

        $mime = $this->validateUpload($file, array_keys(self::IMAGE_EXTENSIONS));
        $dimensions = @getimagesize((string) $file['tmp_name']);
        if ($dimensions === false || ($dimensions[0] * $dimensions[1]) > self::MAX_IMAGE_PIXELS) {
            throw new RuntimeException('La imagen esta danada o excede las dimensiones permitidas.');
        }

        $extension = self::IMAGE_EXTENSIONS[$mime];
        $baseName = bin2hex(random_bytes(16));
        $folder = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $subfolder;
        $this->ensureFolder($folder);
        $destination = $folder . DIRECTORY_SEPARATOR . $baseName . '.' . $extension;

        if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
            throw new RuntimeException('No fue posible guardar la imagen en el servidor.');
        }
        @chmod($destination, 0640);

        try {
            $this->createThumbnail($destination, $folder . DIRECTORY_SEPARATOR . $baseName . '_thumb.jpg');
        } catch (\Throwable $exception) {
            log_exception($exception, ['service' => self::class, 'operation' => 'thumbnail']);
        }

        return 'uploads/' . $subfolder . '/' . basename($destination);
    }

    public function uploadPdf(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $this->validateUpload($file, ['application/pdf']);
        $handle = fopen((string) $file['tmp_name'], 'rb');
        $header = $handle !== false ? fread($handle, 5) : false;
        if (is_resource($handle)) {
            fclose($handle);
        }
        if ($header !== '%PDF-') {
            throw new RuntimeException('El documento no contiene una estructura PDF valida.');
        }

        $folder = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'historial';
        $this->ensureFolder($folder);
        $filename = bin2hex(random_bytes(16)) . '.pdf';
        $destination = $folder . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
            throw new RuntimeException('No fue posible guardar el PDF en el servidor.');
        }
        @chmod($destination, 0640);

        return 'uploads/historial/' . $filename;
    }

    public static function thumbnailPath(?string $original): ?string
    {
        if ($original === null || $original === '') {
            return null;
        }

        $info = pathinfo($original);
        $candidate = ($info['dirname'] !== '.' ? $info['dirname'] . '/' : '') . $info['filename'] . '_thumb.jpg';
        $absolute = PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $candidate);
        return is_file($absolute) ? $candidate : $original;
    }

    /**
     * @param array<int,string> $allowedTypes
     */
    private function validateUpload(array $file, array $allowedTypes): string
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->uploadErrorMessage($error));
        }
        $temporary = (string) ($file['tmp_name'] ?? '');
        if ($temporary === '' || !is_uploaded_file($temporary)) {
            throw new RuntimeException('El archivo no proviene de una carga HTTP valida.');
        }
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_SIZE) {
            throw new RuntimeException('El archivo debe pesar entre 1 byte y 2 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporary);
        if (!is_string($mime) || !in_array($mime, $allowedTypes, true)) {
            throw new RuntimeException('El contenido real del archivo no corresponde a un formato permitido.');
        }

        return $mime;
    }

    private function createThumbnail(string $source, string $destination): void
    {
        $binary = trim((string) shell_exec('command -v magick 2>/dev/null'));
        if ($binary === '') {
            return;
        }

        $command = escapeshellarg($binary)
            . ' ' . escapeshellarg($source)
            . ' -auto-orient -strip -thumbnail 160x160^ -gravity center -extent 160x160 -quality 86 '
            . escapeshellarg($destination) . ' 2>&1';
        exec($command, $output, $exitCode);
        if ($exitCode !== 0 || !is_file($destination)) {
            throw new RuntimeException('No fue posible crear la miniatura: ' . implode(' ', $output));
        }
        @chmod($destination, 0640);
    }

    private function ensureFolder(string $folder): void
    {
        if (!is_dir($folder) && !mkdir($folder, 0750, true) && !is_dir($folder)) {
            throw new RuntimeException('No se pudo crear la carpeta segura de archivos.');
        }
        if (!is_writable($folder)) {
            throw new RuntimeException('La carpeta de archivos no tiene permisos de escritura.');
        }
    }

    private function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo excede el limite permitido.',
            UPLOAD_ERR_PARTIAL => 'El archivo se recibio de forma incompleta.',
            UPLOAD_ERR_NO_FILE => 'No se selecciono ningun archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'El servidor no tiene carpeta temporal.',
            UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo escribir el archivo.',
            UPLOAD_ERR_EXTENSION => 'Una extension del servidor detuvo la carga.',
            default => 'Ocurrio un error durante la carga del archivo.',
        };
    }
}
