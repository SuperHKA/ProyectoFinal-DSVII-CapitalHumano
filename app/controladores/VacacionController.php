<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Vacacion;
use App\Servicios\AuthService;
use App\Servicios\PdfResolutionService;
use App\Utilidades\FormState;
use App\Utilidades\Sanitizer;
use App\Utilidades\Validator;
use Throwable;

/**
 * Controla solicitudes de vacaciones.
 */
final class VacacionController
{
    private Vacacion $vacaciones;
    private PdfResolutionService $pdf;

    public function __construct()
    {
        $this->vacaciones = new Vacacion();
        $this->pdf = new PdfResolutionService();
    }

    public function index(): void
    {
        AuthService::ensure('vacaciones', 'ver');
        $page = max(1, Sanitizer::int($_GET['page'] ?? 1));
        $pagination = $this->vacaciones->paginated($page, 15);
        render_view('vacaciones/index', [
            'title' => 'Vacaciones',
            'solicitudes' => $pagination['items'],
            'paginacion' => $pagination,
            'resumen' => $this->vacaciones->summary(),
        ]);
    }

    public function create(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        render_view('vacaciones/create', [
            'title' => 'Nueva solicitud de vacaciones',
            'colaboradores' => $this->vacaciones->colaboradoresDisponibles(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function store(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        try {
            require_post();
            verify_csrf();
            $data = [
                'colaborador_id' => Sanitizer::int($_POST['colaborador_id'] ?? 0),
                'fecha_inicio' => Sanitizer::text($_POST['fecha_inicio'] ?? ''),
                'fecha_fin' => Sanitizer::text($_POST['fecha_fin'] ?? ''),
                'observaciones' => Sanitizer::text($_POST['observaciones'] ?? ''),
            ];
            $errors = Validator::vacacion($data);
            if ($errors) {
                FormState::flash($data, $errors);
                redirect_to('vacaciones.create');
            }

            $this->vacaciones->create($data);
            flash('success', 'Solicitud registrada correctamente.');
            redirect_to('vacaciones.index');
        } catch (Throwable $exception) {
            $message = $exception instanceof \RuntimeException ? $exception->getMessage() : 'No fue posible registrar la solicitud.';
            FormState::flash($_POST, [safe_error_message($exception, $message, ['module' => 'vacaciones', 'action' => 'create'])]);
            redirect_to('vacaciones.create');
        }
    }

    public function aprobar(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        try {
            require_post();
            verify_csrf();
            $this->vacaciones->aprobar(
                Sanitizer::int($_GET['id'] ?? 0),
                (int) (AuthService::user()['id'] ?? 0)
            );
            flash('success', 'Solicitud aprobada.');
        } catch (Throwable $exception) {
            $message = $exception instanceof \RuntimeException ? $exception->getMessage() : 'No fue posible aprobar la solicitud.';
            flash('error', safe_error_message($exception, $message, ['module' => 'vacaciones', 'action' => 'approve']));
        }
        redirect_to('vacaciones.index');
    }

    public function rechazar(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        try {
            require_post();
            verify_csrf();
            $this->vacaciones->rechazar(Sanitizer::int($_GET['id'] ?? 0));
            flash('success', 'Solicitud rechazada.');
        } catch (Throwable $exception) {
            $message = $exception instanceof \RuntimeException ? $exception->getMessage() : 'No fue posible rechazar la solicitud.';
            flash('error', safe_error_message($exception, $message, ['module' => 'vacaciones', 'action' => 'reject']));
        }
        redirect_to('vacaciones.index');
    }

    public function resuelto(): never
    {
        AuthService::ensure('vacaciones', 'ver');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        try {
            $resolution = $this->vacaciones->resolution($id);
            if ($resolution === null || $resolution['estado'] !== 'Aprobada' || empty($resolution['numero_resuelto'])) {
                throw new \RuntimeException('El resuelto solicitado no esta disponible.');
            }
            $pdf = $this->pdf->render($resolution);
            $filename = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $resolution['numero_resuelto']) . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($pdf));
            header('Cache-Control: private, no-store');
            echo $pdf;
            exit;
        } catch (Throwable $exception) {
            $message = $exception instanceof \RuntimeException ? $exception->getMessage() : 'No fue posible generar el resuelto.';
            flash('error', safe_error_message($exception, $message, ['module' => 'vacaciones', 'action' => 'pdf']));
            redirect_to('vacaciones.index');
        }
    }
}
