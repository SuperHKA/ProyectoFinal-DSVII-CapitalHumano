<?php

declare(strict_types=1);

use App\Modelos\Colaborador;
use Config\Database;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

$defaults = [
    'segundo_nombre' => '',
    'segundo_apellido' => '',
    'telefono' => '200-0000',
    'estado_colaborador_id' => 1,
    'tipo_empleado_id' => 7,
    'planilla_id' => 1,
    'fotografia' => null,
];

$records = [
    [
        'categoria' => 'Activo',
        'persona' => [
            'identidad' => '8-1001-0001',
            'primer_nombre' => 'Ana',
            'segundo_nombre' => 'María',
            'primer_apellido' => 'Martínez',
            'segundo_apellido' => 'López',
            'fecha_nacimiento' => '1993-05-18',
            'sexo' => 'Femenino',
            'direccion' => 'San Francisco, Ciudad de Panamá',
            'correo' => 'ana.martinez@capitaldemo.local',
            'telefono' => '203-1101',
            'celular' => '6100-1101',
        ],
        'perfil' => [
            'ocupacion_id' => 23,
            'departamento_id' => 1,
            'salario' => 1850.00,
            'fecha_inicio' => '2024-04-01',
        ],
    ],
    [
        'categoria' => 'Activo',
        'persona' => [
            'identidad' => '8-1001-0002',
            'primer_nombre' => 'Carlos',
            'segundo_nombre' => 'Andrés',
            'primer_apellido' => 'Rivera',
            'segundo_apellido' => 'Pérez',
            'fecha_nacimiento' => '1989-11-02',
            'sexo' => 'Masculino',
            'direccion' => 'Brisas del Golf, San Miguelito',
            'correo' => 'carlos.rivera@capitaldemo.local',
            'telefono' => '203-1102',
            'celular' => '6100-1102',
        ],
        'perfil' => [
            'ocupacion_id' => 17,
            'departamento_id' => 2,
            'salario' => 1675.00,
            'fecha_inicio' => '2023-09-18',
        ],
    ],
    [
        'categoria' => 'Activo',
        'persona' => [
            'identidad' => '8-1001-0003',
            'primer_nombre' => 'Lucía',
            'primer_apellido' => 'Gómez',
            'segundo_apellido' => 'Castro',
            'fecha_nacimiento' => '1987-02-21',
            'sexo' => 'Femenino',
            'direccion' => 'El Dorado, Ciudad de Panamá',
            'correo' => 'lucia.gomez@capitaldemo.local',
            'telefono' => '203-1103',
            'celular' => '6100-1103',
        ],
        'perfil' => [
            'ocupacion_id' => 14,
            'departamento_id' => 3,
            'salario' => 1725.00,
            'fecha_inicio' => '2022-08-08',
        ],
    ],
    [
        'categoria' => 'Activo',
        'persona' => [
            'identidad' => '8-1001-0004',
            'primer_nombre' => 'Miguel',
            'segundo_nombre' => 'Ángel',
            'primer_apellido' => 'Santos',
            'segundo_apellido' => 'Ruiz',
            'fecha_nacimiento' => '1995-08-09',
            'sexo' => 'Masculino',
            'direccion' => 'Juan Díaz, Ciudad de Panamá',
            'correo' => 'miguel.santos@capitaldemo.local',
            'telefono' => '203-1104',
            'celular' => '6100-1104',
        ],
        'perfil' => [
            'ocupacion_id' => 16,
            'departamento_id' => 5,
            'salario' => 1650.00,
            'fecha_inicio' => '2024-01-15',
        ],
    ],
    [
        'categoria' => 'Promocionado y activo',
        'persona' => [
            'identidad' => '8-1001-0005',
            'primer_nombre' => 'Sofía',
            'segundo_nombre' => 'Isabel',
            'primer_apellido' => 'Hernández',
            'segundo_apellido' => 'Vega',
            'fecha_nacimiento' => '1991-04-12',
            'sexo' => 'Femenino',
            'direccion' => 'Betania, Ciudad de Panamá',
            'correo' => 'sofia.hernandez@capitaldemo.local',
            'telefono' => '203-1105',
            'celular' => '6100-1105',
        ],
        'perfil' => [
            'ocupacion_id' => 23,
            'departamento_id' => 1,
            'salario' => 1800.00,
            'fecha_inicio' => '2022-09-05',
        ],
        'promocion' => [
            'ocupacion_id' => 145,
            'tipo_empleado_id' => 7,
            'planilla_id' => 1,
            'departamento_id' => 1,
            'salario' => 2650.00,
            'fecha_inicio' => '2025-02-01',
        ],
    ],
    [
        'categoria' => 'Promocionado y activo',
        'persona' => [
            'identidad' => '8-1001-0006',
            'primer_nombre' => 'Diego',
            'segundo_nombre' => 'Alejandro',
            'primer_apellido' => 'Castillo',
            'segundo_apellido' => 'Moreno',
            'fecha_nacimiento' => '1990-07-27',
            'sexo' => 'Masculino',
            'direccion' => 'Costa del Este, Ciudad de Panamá',
            'correo' => 'diego.castillo@capitaldemo.local',
            'telefono' => '203-1106',
            'celular' => '6100-1106',
        ],
        'perfil' => [
            'ocupacion_id' => 18,
            'departamento_id' => 3,
            'salario' => 1780.00,
            'fecha_inicio' => '2023-03-20',
        ],
        'promocion' => [
            'ocupacion_id' => 19,
            'tipo_empleado_id' => 7,
            'planilla_id' => 1,
            'departamento_id' => 3,
            'salario' => 2280.00,
            'fecha_inicio' => '2025-10-01',
        ],
    ],
    [
        'categoria' => 'Retirado por jubilación',
        'persona' => [
            'identidad' => '8-1001-0007',
            'primer_nombre' => 'Roberto',
            'segundo_nombre' => 'José',
            'primer_apellido' => 'Díaz',
            'segundo_apellido' => 'Méndez',
            'fecha_nacimiento' => '1960-01-19',
            'sexo' => 'Masculino',
            'direccion' => 'La Chorrera, Panamá Oeste',
            'correo' => 'roberto.diaz@capitaldemo.local',
            'telefono' => '203-1107',
            'celular' => '6100-1107',
        ],
        'perfil' => [
            'ocupacion_id' => 2,
            'departamento_id' => 8,
            'salario' => 2200.00,
            'fecha_inicio' => '2022-03-14',
        ],
        'baja' => [
            'fecha_fin' => '2025-11-28',
            'motivo_terminacion_id' => 1,
        ],
    ],
    [
        'categoria' => 'Retirada por pensión',
        'persona' => [
            'identidad' => '8-1001-0008',
            'primer_nombre' => 'Marta',
            'segundo_nombre' => 'Elena',
            'primer_apellido' => 'Morales',
            'segundo_apellido' => 'Ríos',
            'fecha_nacimiento' => '1962-10-06',
            'sexo' => 'Femenino',
            'direccion' => 'Arraiján, Panamá Oeste',
            'correo' => 'marta.morales@capitaldemo.local',
            'telefono' => '203-1108',
            'celular' => '6100-1108',
        ],
        'perfil' => [
            'ocupacion_id' => 20,
            'departamento_id' => 2,
            'salario' => 2050.00,
            'fecha_inicio' => '2022-07-04',
        ],
        'baja' => [
            'fecha_fin' => '2026-02-27',
            'motivo_terminacion_id' => 4,
        ],
    ],
    [
        'categoria' => 'Baja por terminación de contrato',
        'persona' => [
            'identidad' => '8-1001-0009',
            'primer_nombre' => 'Elena',
            'primer_apellido' => 'Vargas',
            'segundo_apellido' => 'Quintero',
            'fecha_nacimiento' => '1992-12-14',
            'sexo' => 'Femenino',
            'direccion' => 'Tocumen, Ciudad de Panamá',
            'correo' => 'elena.vargas@capitaldemo.local',
            'telefono' => '203-1109',
            'celular' => '6100-1109',
        ],
        'perfil' => [
            'ocupacion_id' => 15,
            'tipo_empleado_id' => 2,
            'planilla_id' => 2,
            'departamento_id' => 4,
            'salario' => 1550.00,
            'fecha_inicio' => '2023-02-06',
        ],
        'baja' => [
            'fecha_fin' => '2025-09-30',
            'motivo_terminacion_id' => 6,
        ],
    ],
    [
        'categoria' => 'Baja administrativa',
        'persona' => [
            'identidad' => '8-1001-0010',
            'primer_nombre' => 'Javier',
            'segundo_nombre' => 'Enrique',
            'primer_apellido' => 'Mendoza',
            'segundo_apellido' => 'Salazar',
            'fecha_nacimiento' => '1988-06-30',
            'sexo' => 'Masculino',
            'direccion' => 'Río Abajo, Ciudad de Panamá',
            'correo' => 'javier.mendoza@capitaldemo.local',
            'telefono' => '203-1110',
            'celular' => '6100-1110',
        ],
        'perfil' => [
            'ocupacion_id' => 10,
            'departamento_id' => 4,
            'salario' => 1475.00,
            'fecha_inicio' => '2024-01-22',
        ],
        'baja' => [
            'fecha_fin' => '2026-04-30',
            'motivo_terminacion_id' => 3,
        ],
    ],
];

$db = Database::getConnection();
$model = new Colaborador($db);
$findExisting = $db->prepare(
    'SELECT id FROM colaboradores WHERE identidad = :identidad OR correo = :correo LIMIT 1'
);
$created = 0;
$skipped = 0;

foreach ($records as $record) {
    $person = array_merge($defaults, $record['persona'], $record['perfil']);
    $findExisting->execute([
        'identidad' => $person['identidad'],
        'correo' => $person['correo'],
    ]);

    if ($findExisting->fetchColumn() !== false) {
        $skipped++;
        echo "OMITIDO: {$person['primer_nombre']} {$person['primer_apellido']} ya existe.\n";
        continue;
    }

    $id = null;
    try {
        $id = $model->create($person);

        if (isset($record['promocion'])) {
            $model->promote($id, $record['promocion']);
        }
        if (isset($record['baja'])) {
            $model->baja($id, $record['baja']);
        }

        $created++;
        echo "CREADO #{$id}: {$person['primer_nombre']} {$person['primer_apellido']} — {$record['categoria']}.\n";
    } catch (Throwable $exception) {
        if ($id !== null) {
            try {
                $model->delete($id);
            } catch (Throwable) {
                // La excepción original contiene la causa útil del fallo del seeder.
            }
        }
        throw $exception;
    }
}

echo "Resumen: {$created} creados, {$skipped} omitidos.\n";
