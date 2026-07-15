<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Contratos\CryptographicServiceInterface;
use RuntimeException;
use Throwable;

/**
 * Canoniza perfiles laborales y delega la criptografia al contrato comun.
 */
final class IntegrityService
{
    private CryptographicServiceInterface $signer;

    public function __construct(?CryptographicServiceInterface $signer = null)
    {
        $this->signer = $signer ?? new RsaSignatureService();
    }

    /**
     * @param array<string,mixed> $data
     */
    public function canonicalPerfilLaboral(array $data): string
    {
        $payload = [
            'identidad' => (string) ($data['identidad'] ?? ''),
            'codigo_empleado' => (int) ($data['colaborador_id'] ?? $data['codigo_empleado'] ?? 0),
            'salario' => number_format((float) ($data['salario'] ?? 0), 2, '.', ''),
            'tipo_empleado_id' => (int) ($data['tipo_empleado_id'] ?? 0),
            'planilla_id' => (int) ($data['planilla_id'] ?? 0),
            'departamento_id' => (int) ($data['departamento_id'] ?? 0),
            'ocupacion_id' => (int) ($data['ocupacion_id'] ?? 0),
            'fecha_inicio' => (string) ($data['fecha_inicio'] ?? ''),
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('No fue posible construir los datos sensibles para firmar.');
        }

        return $json;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function signPerfilLaboral(array $data): string
    {
        return $this->signer->protect($this->canonicalPerfilLaboral($data));
    }

    /**
     * @param array<string,mixed> $data
     * @return array{valid:bool,message:string}
     */
    public function verifyPerfilLaboral(array $data, ?string $signature): array
    {
        if ($signature === null || trim($signature) === '') {
            return ['valid' => false, 'message' => 'Firma ausente'];
        }

        try {
            $valid = $this->signer->verify($this->canonicalPerfilLaboral($data), $signature);
            return [
                'valid' => $valid,
                'message' => $valid ? 'Integridad completa' : 'Registro vulnerado',
            ];
        } catch (Throwable $exception) {
            log_exception($exception, ['service' => self::class, 'operation' => 'verify']);
            return ['valid' => false, 'message' => 'Error al verificar firma'];
        }
    }
}
