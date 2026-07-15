<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Colaborador;
use App\Servicios\AuthService;
use App\Servicios\ExcelExportService;
use App\Utilidades\Sanitizer;

/**
 * Genera el reporte visual, estadisticas y exportable de colaboradores.
 */
final class ReporteController
{
    public function index(): void
    {
        AuthService::ensure('reportes', 'ver');
        $filters = $this->filters();
        $page = Sanitizer::int($_GET['page'] ?? 1);
        $page = $page < 1 ? 1 : $page;

        $modelo = new Colaborador();
        $paginacion = $modelo->paginated(
            $filters['q'],
            $page,
            15,
            $filters['sexo'],
            $filters['edad'],
            $filters['salario_min'],
            $filters['salario_max'],
            $filters['direccion']
        );
        render_view('reportes/index', [
            'title' => 'Reporte de colaboradores',
            'rows' => $paginacion['items'],
            'paginacion' => $paginacion,
            'por_sexo' => $modelo->estadisticasPorSexo(),
            'por_edad' => $modelo->estadisticasPorEdad(),
            'por_direccion' => $modelo->estadisticasPorDireccion(),
        ] + $filters);
    }

    public function export(): void
    {
        AuthService::ensure('reportes', 'exportar');
        $filters = $this->filters();
        (new ExcelExportService())->download(
            (new Colaborador())->report(
                $filters['q'],
                $filters['sexo'],
                $filters['edad'],
                $filters['salario_min'],
                $filters['salario_max'],
                $filters['direccion']
            )
        );
    }

    /**
     * @return array{q:string,sexo:string,edad:string,direccion:string,salario_min:?float,salario_max:?float}
     */
    private function filters(): array
    {
        $filters = [
            'q' => Sanitizer::text($_GET['q'] ?? ''),
            'sexo' => Sanitizer::text($_GET['sexo'] ?? ''),
            'edad' => Sanitizer::text($_GET['edad'] ?? ''),
            'direccion' => Sanitizer::text($_GET['direccion'] ?? ''),
            'salario_min' => Sanitizer::nullableMoney($_GET['salario_min'] ?? ''),
            'salario_max' => Sanitizer::nullableMoney($_GET['salario_max'] ?? ''),
        ];

        if ($filters['salario_min'] !== null
            && $filters['salario_max'] !== null
            && $filters['salario_min'] > $filters['salario_max']) {
            [$filters['salario_min'], $filters['salario_max']] = [$filters['salario_max'], $filters['salario_min']];
        }

        return $filters;
    }
}
