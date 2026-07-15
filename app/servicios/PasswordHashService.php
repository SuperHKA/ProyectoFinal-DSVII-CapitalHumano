<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Contratos\CryptographicServiceInterface;
use RuntimeException;

final class PasswordHashService implements CryptographicServiceInterface
{
    public function protect(string $plainText): string
    {
        $hash = password_hash($plainText, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('No fue posible proteger la contrasena.');
        }

        return $hash;
    }

    public function verify(string $plainText, string $protectedValue): bool
    {
        return password_verify($plainText, $protectedValue);
    }

    public function algorithm(): string
    {
        return 'PASSWORD_DEFAULT';
    }
}
