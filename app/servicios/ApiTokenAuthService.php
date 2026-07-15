<?php

declare(strict_types=1);

namespace App\Servicios;

use RuntimeException;

final class ApiTokenAuthService
{
    public function authenticate(): void
    {
        $configuredToken = trim((string) env_value('CONTRALORIA_API_TOKEN', ''));
        if ($configuredToken === '') {
            throw new RuntimeException('API_TOKEN_NOT_CONFIGURED', 503);
        }

        $providedToken = $this->providedToken();
        if ($providedToken === '' || !hash_equals(hash('sha256', $configuredToken), hash('sha256', $providedToken))) {
            throw new RuntimeException('INVALID_API_TOKEN', 401);
        }
    }

    private function providedToken(): string
    {
        $authorization = trim((string) ($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
        if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches) === 1) {
            return trim($matches[1]);
        }

        return trim((string) ($_SERVER['HTTP_X_API_KEY'] ?? ''));
    }
}
