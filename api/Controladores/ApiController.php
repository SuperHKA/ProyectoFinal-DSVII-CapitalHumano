<?php

declare(strict_types=1);

namespace Api\Controladores;

use App\Modelos\Colaborador;
use App\Servicios\ApiTokenAuthService;
use App\Servicios\FileLogger;
use RuntimeException;
use Throwable;

/**
 * API REST para entregar datos a la Contraloria General.
 */
final class ApiController
{
    public function __construct(private ?ApiTokenAuthService $auth = null)
    {
        $this->auth ??= new ApiTokenAuthService();
    }

    public function porSexo(): never
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        try {
            if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
                header('Allow: GET');
                throw new RuntimeException('METHOD_NOT_ALLOWED', 405);
            }

            $this->auth->authenticate();
            $data = (new Colaborador())->estadisticasPorSexo();
            $this->json([
                'fuente' => 'Sistema de Capital Humano',
                'destinatario' => 'Contraloria General de la Republica',
                'fecha' => date(DATE_ATOM),
                'total_colaboradores' => array_sum(array_column($data, 'total')),
                'por_sexo' => $data,
            ], 200);
        } catch (RuntimeException $exception) {
            $status = in_array($exception->getCode(), [401, 405, 503], true) ? $exception->getCode() : 500;
            $messages = [
                401 => ['unauthorized', 'Credencial de Contraloria ausente o invalida.'],
                405 => ['method_not_allowed', 'Este recurso solo admite solicitudes GET.'],
                503 => ['service_unavailable', 'La credencial de integracion no esta configurada.'],
            ];
            [$code, $message] = $messages[$status] ?? ['internal_error', 'No fue posible procesar la solicitud.'];
            if ($status === 401) {
                header('WWW-Authenticate: Bearer realm="Contraloria"');
            }
            if ($status !== 401) {
                (new FileLogger())->error($exception, ['api' => 'colaboradores_por_sexo']);
            }
            $this->json(['error' => $code, 'message' => $message], $status);
        } catch (Throwable $exception) {
            $incidentId = (new FileLogger())->error($exception, ['api' => 'colaboradores_por_sexo']);
            $this->json([
                'error' => 'internal_error',
                'message' => 'No fue posible procesar la solicitud.',
                'incident_id' => $incidentId,
            ], 500);
        }

        exit;
    }

    /**
     * @param array<string,mixed> $payload
     */
    private function json(array $payload, int $status): never
    {
        http_response_code($status);
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
