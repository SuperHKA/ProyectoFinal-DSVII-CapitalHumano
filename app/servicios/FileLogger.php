<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Contratos\LoggerInterface;
use Throwable;

final class FileLogger implements LoggerInterface
{
    private string $logDirectory;

    public function __construct(?string $logDirectory = null)
    {
        $this->logDirectory = $logDirectory
            ?? ROOT_PATH . DIRECTORY_SEPARATOR . 'almacenamiento' . DIRECTORY_SEPARATOR . 'logs';
    }

    public function error(Throwable|string $error, array $context = []): string
    {
        $incidentId = bin2hex(random_bytes(6));
        $this->ensureDirectory();

        $record = [
            'timestamp' => date(DATE_ATOM),
            'incident_id' => $incidentId,
            'level' => 'ERROR',
            'message' => $error instanceof Throwable ? $error->getMessage() : $error,
            'exception' => $error instanceof Throwable ? $error::class : null,
            'file' => $error instanceof Throwable ? $error->getFile() : null,
            'line' => $error instanceof Throwable ? $error->getLine() : null,
            'route' => $context['route'] ?? ($_GET['route'] ?? null),
            'method' => $_SERVER['REQUEST_METHOD'] ?? PHP_SAPI,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_id' => $_SESSION['usuario_id'] ?? null,
            'context' => $this->sanitizeContext($context),
            'trace' => $error instanceof Throwable ? $error->getTraceAsString() : null,
        ];

        $encoded = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded !== false) {
            $path = $this->logDirectory . DIRECTORY_SEPARATOR . 'errors-' . date('Y-m-d') . '.log';
            if (is_file($path) && !is_writable($path)) {
                $worker = function_exists('posix_geteuid') ? (string) posix_geteuid() : PHP_SAPI;
                $path = $this->logDirectory . DIRECTORY_SEPARATOR . 'errors-' . date('Y-m-d') . '-' . $worker . '.log';
            }
            $written = @file_put_contents($path, $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);
            if ($written !== false) {
                @chmod($path, 0640);
            }
        }

        return $incidentId;
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0750, true);
        }
    }

    /**
     * @param array<string,mixed> $context
     * @return array<string,mixed>
     */
    private function sanitizeContext(array $context): array
    {
        $sensitive = ['password', 'password_hash', 'csrf_token', 'token', 'authorization', 'api_key'];
        $safe = [];
        foreach ($context as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $safe[$key] = '[REDACTED]';
                continue;
            }
            $safe[$key] = is_scalar($value) || $value === null ? $value : get_debug_type($value);
        }

        return $safe;
    }
}
