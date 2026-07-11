<?php
$action = url('colaboradores.update', ['id' => $colaborador['id']]);
$button = 'Actualizar colaborador';
$titulo = 'Editar colaborador';
$subtitulo = trim(($colaborador['primer_nombre'] ?? '') . ' ' . ($colaborador['primer_apellido'] ?? ''));
require __DIR__ . DIRECTORY_SEPARATOR . '_form.php';
?>
