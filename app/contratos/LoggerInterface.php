<?php

declare(strict_types=1);

namespace App\Contratos;

use Throwable;

interface LoggerInterface
{
    /**
     * @param array<string,mixed> $context
     */
    public function error(Throwable|string $error, array $context = []): string;
}
