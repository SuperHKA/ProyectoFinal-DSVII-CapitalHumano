<?php

declare(strict_types=1);

namespace App\Contratos;

/**
 * Contrato comun para transformar y verificar datos protegidos.
 *
 * Una implementacion puede producir un hash unidireccional (contrasenas)
 * o una firma verificable con llave publica (integridad de registros).
 */
interface CryptographicServiceInterface
{
    public function protect(string $plainText): string;

    public function verify(string $plainText, string $protectedValue): bool;

    public function algorithm(): string;
}
