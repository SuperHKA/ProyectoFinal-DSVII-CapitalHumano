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
        $q = Sanitizer::text($_GET['q'] ?? '');
        $sexo = Sanitizer::text($_GET['sexo'] ?? '');
        $edad = Sanitizer::text($_GET['edad'] ?? '');
        $direccion = Sanitizer::text($_GET['direccion'] ?? '');
        $salarioMin = Sanitizer::nullableMoney($_GET['salario_min'] ?? '');
        $salarioMax = Sanitizer::nullableMoney($_GET['salario_max'] ?? '');
        if ($salarioMin !== null && $salarioMax !== null && $salarioMin > $salarioMax) {
            [$salarioMin, $salarioMax] = [$salarioMax, $salarioMin];
        }
        $page = Sanitizer::int($_GET['page'] ?? 1);
        $page = $page < 1 ? 1 : $page;

        $modelo = new Colaborador();
        $paginacion = $modelo->paginated($q, $page, 15, $sexo, $edad, $salarioMin, $salarioMax, $direccion);
        render_view('reportes/index', [
            'title' => 'Reporte de colaboradores',
            'rows' => $paginacion['items'],
            'paginacion' => $paginacion,
            'q' => $q,
            'sexo' => $sexo,
            'edad' => $edad,
            'direccion' => $direccion,
            'salario_min' => $salarioMin,
            'salario_max' => $salarioMax,
            'por_sexo' => $modelo->estadisticasPorSexo(),
            'por_edad' => $modelo->estadisticasPorEdad(),
            'por_direccion' => $modelo->estadisticasPorDireccion(),
        ]);
    }

    public function export(): void
    {
        AuthService::ensure('reportes', 'exportar');
        $q = Sanitizer::text($_GET['q'] ?? '');
        $sexo = Sanitizer::text($_GET['sexo'] ?? '');
        $edad = Sanitizer::text($_GET['edad'] ?? '');
        $direccion = Sanitizer::text($_GET['direccion'] ?? '');
        $salarioMin = Sanitizer::nullableMoney($_GET['salario_min'] ?? '');
        $salarioMax = Sanitizer::nullableMoney($_GET['salario_max'] ?? '');
        if ($salarioMin !== null && $salarioMax !== null && $salarioMin > $salarioMax) {
            [$salarioMin, $salarioMax] = [$salarioMax, $salarioMin];
        }
        (new ExcelExportService())->download(
            (new Colaborador())->report($q, $sexo, $edad, $salarioMin, $salarioMax, $direccion)
        );
    }
}
