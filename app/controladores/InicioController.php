<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Colaborador;
use App\Servicios\AuthService;

/**
 * Sirve la pantalla de Inicio (panel general con KPIs y graficos).
 */
final class InicioController
{
    private Colaborador $colaboradores;

    public function __construct()
    {
        $this->colaboradores = new Colaborador();
    }

    public function index(): void
    {
        AuthService::requireAuth();

        $todos = $this->colaboradores->all();
        $total = count($todos);
        $activos = array_filter($todos, static fn(array $c): bool => (int) ($c['empleado_activo'] ?? 0) === 1);

        $stats = [
            'colaboradores' => $total,
            'activos'       => count($activos),
            'por_sexo'      => $this->colaboradores->estadisticasPorSexo(),
            'por_edad'      => $this->colaboradores->estadisticasPorEdad(),
        ];

        render_view('inicio/index', [
            'title' => 'Inicio',
            'stats' => $stats,
        ]);
    }
}
