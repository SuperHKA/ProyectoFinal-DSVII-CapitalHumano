<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Contratos\ErrorHandlerInterface;
use App\Contratos\LoggerInterface;
use Throwable;

/**
 * Maneja errores web registrandolos y mostrando un mensaje al usuario.
 */
final class WebErrorHandler implements ErrorHandlerInterface
{
    public function __construct(private ?LoggerInterface $logger = null)
    {
        $this->logger ??= new FileLogger();
    }

    public function handle(Throwable $exception): void
    {
        $incidentId = $this->logger->error($exception);
        flash('error', 'Ocurrio un error inesperado. Referencia: ' . $incidentId . '.');
    }
}
