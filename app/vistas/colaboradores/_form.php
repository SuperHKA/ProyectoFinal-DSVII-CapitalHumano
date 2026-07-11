<?php
$defaults = [
    'identidad' => '',
    'primer_nombre' => '',
    'segundo_nombre' => '',
    'primer_apellido' => '',
    'segundo_apellido' => '',
    'fecha_nacimiento' => '',
    'sexo' => '',
    'direccion' => '',
    'correo' => '',
    'telefono' => '',
    'celular' => '',
    'estado_colaborador_id' => 1,
    'ocupacion_id' => '',
    'tipo_empleado_id' => '',
    'planilla_id' => '',
    'departamento_id' => '',
    'salario' => '',
    'fecha_inicio' => '',
    'fecha_fin' => '',
];
$values = array_merge($defaults, $colaborador ?? [], $old ?? []);

if (!empty($colaborador['perfiles'][0])) {
    $perfilReciente = $colaborador['perfiles'][0];
    foreach (['ocupacion_id', 'tipo_empleado_id', 'planilla_id', 'departamento_id', 'salario', 'fecha_inicio', 'fecha_fin'] as $campo) {
        if (($values[$campo] ?? '') === '' && isset($perfilReciente[$campo]) && $perfilReciente[$campo] !== null) {
            $values[$campo] = $perfilReciente[$campo];
        }
    }
}

$req = static fn(bool $required) => $required ? ' <span class="req" aria-hidden="true">*</span>' : '';

// Mapear errores a un dict campo => mensaje para resaltar inputs
$fieldErrors = [];
if (!empty($errors)) {
    foreach ($errors as $err) {
        if (preg_match('/^([a-z_]+)\s+es\s+obligatorio\.?$/', $err, $m)) {
            $fieldErrors[$m[1]] = 'Obligatorio';
        } elseif (preg_match('/^El campo ([a-z_]+) (.+)$/', $err, $m)) {
            $fieldErrors[$m[1]] = $m[2];
        } elseif (preg_match('/^([A-Za-z_]+) debe (.+)$/', $err, $m)) {
            $fieldErrors[$m[1]] = $m[2];
        }
    }
}
$errFor = static function (string $name) use ($fieldErrors): ?string {
    if (isset($fieldErrors[$name])) return $fieldErrors[$name];
    $aliases = [
        'primer_nombre' => 'primer_nombre',
        'segundo_nombre' => 'segundo_nombre',
        'primer_apellido' => 'primer_apellido',
        'segundo_apellido' => 'segundo_apellido',
        'identidad' => 'identidad',
        'fecha_nacimiento' => 'fecha_nacimiento',
        'sexo' => 'sexo',
        'direccion' => 'direccion',
        'correo' => 'correo',
        'telefono' => 'telefono',
        'celular' => 'celular',
        'estado_colaborador_id' => 'estado',
        'ocupacion_id' => 'ocupacion',
        'tipo_empleado_id' => 'tipo_empleado',
        'planilla_id' => 'planilla',
        'departamento_id' => 'departamento',
        'salario' => 'salario',
        'fecha_inicio' => 'fecha_inicio',
        'fecha_fin' => 'fecha_fin',
    ];
    return $fieldErrors[$aliases[$name] ?? $name] ?? null;
};
$fieldClass = static function (string $name) use ($errFor): string {
    return $errFor($name) ? ' field has-error' : '';
};
$errorPill = static function (string $name) use ($errFor): string {
    $msg = $errFor($name);
    return $msg ? '<span class="field-error">'.e(ucfirst($msg)).'</span>' : '';
};
?>

<?php if (!empty($errors)): ?>
    <div class="form-page__errors" role="alert">
        <strong><?= count($errors) === 1 ? '1 error encontrado' : count($errors) . ' errores encontrados' ?></strong>
        <ul>
            <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <div class="form-page">
        <div class="form-page__intro">
            <h1><?= e($titulo ?? 'Formulario de Informacion Personal y Laboral') ?></h1>
            <p><?= e($subtitulo ?? 'Por favor, completa todos los campos requeridos con la informacion solicitada.') ?></p>
        </div>

        <section class="form-section-card">
            <header class="form-section-card__head">
                <span class="form-section-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a7 7 0 0 1 7-7h2a7 7 0 0 1 7 7v1"/></svg>
                </span>
                <h2>Datos personales</h2>
            </header>
            <div class="form-section-card__body form-grid">
                <div class="field span-2">
                    <label for="fotografia">Fotografia</label>
                    <label for="fotografia" class="dropzone">
                        <input id="fotografia" type="file" name="fotografia" accept="image/*">
                        <span class="dropzone__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </span>
                        <span class="dropzone__primary">Subir archivo</span>
                        <span class="dropzone__hint">JPG, PNG (max. 5MB)</span>
                    </label>
                </div>

                <div class="field">
                    <label for="identidad">Identidad o documento<?= $req(true) ?></label>
                    <input id="identidad" name="identidad" required value="<?= e($values['identidad']) ?>" placeholder="Numero de identidad o documento">
                </div>

                <div class="field">
                    <label for="primer_nombre">Primer nombre<?= $req(true) ?></label>
                    <input id="primer_nombre" name="primer_nombre" required value="<?= e($values['primer_nombre']) ?>" placeholder="Ingresa tu primer nombre">
                </div>

                <div class="field">
                    <label for="segundo_nombre">Segundo nombre</label>
                    <input id="segundo_nombre" name="segundo_nombre" value="<?= e($values['segundo_nombre']) ?>" placeholder="Ingresa tu segundo nombre">
                </div>

                <div class="field">
                    <label for="primer_apellido">Primer apellido<?= $req(true) ?></label>
                    <input id="primer_apellido" name="primer_apellido" required value="<?= e($values['primer_apellido']) ?>" placeholder="Ingresa tu primer apellido">
                </div>

                <div class="field">
                    <label for="segundo_apellido">Segundo apellido</label>
                    <input id="segundo_apellido" name="segundo_apellido" value="<?= e($values['segundo_apellido']) ?>" placeholder="Ingresa tu segundo apellido">
                </div>

                <div class="field">
                    <label for="fecha_nacimiento">Fecha de nacimiento<?= $req(true) ?></label>
                    <input id="fecha_nacimiento" type="date" name="fecha_nacimiento" required value="<?= e($values['fecha_nacimiento'] ?? '') ?>">
                </div>

                <div class="field">
                    <label for="sexo">Sexo<?= $req(true) ?></label>
                    <div class="select-wrap">
                        <select id="sexo" name="sexo" required>
                            <option value="">Seleccione</option>
                            <?php foreach (['Masculino', 'Femenino', 'Otro'] as $sexo): ?>
                                <option value="<?= e($sexo) ?>" <?= selected($values['sexo'], $sexo) ?>><?= e($sexo) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <section class="form-section-card">
            <header class="form-section-card__head">
                <span class="form-section-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <h2>Contacto y ubicacion</h2>
            </header>
            <div class="form-section-card__body form-grid">
                <div class="field span-2">
                    <label for="direccion">Direccion<?= $req(true) ?></label>
                    <textarea id="direccion" name="direccion" rows="2" required placeholder="Ingresa tu direccion completa"><?= e($values['direccion'] ?? '') ?></textarea>
                </div>

                <div class="field">
                    <label for="correo">Correo electronico<?= $req(true) ?></label>
                    <div class="input-wrap input-wrap--icon">
                        <span class="input-wrap__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                        </span>
                        <input id="correo" type="email" name="correo" required value="<?= e($values['correo']) ?>" placeholder="ejemplo@correo.com">
                    </div>
                </div>

                <div class="field">
                    <label for="telefono">Telefono<?= $req(true) ?></label>
                    <div class="input-wrap input-wrap--icon">
                        <span class="input-wrap__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </span>
                        <input id="telefono" name="telefono" required value="<?= e($values['telefono'] ?? '') ?>" placeholder="Ej. 1234-5678">
                    </div>
                </div>

                <div class="field">
                    <label for="celular">Celular<?= $req(true) ?></label>
                    <div class="input-wrap input-wrap--icon">
                        <span class="input-wrap__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
                        </span>
                        <input id="celular" name="celular" required value="<?= e($values['celular']) ?>" placeholder="Ej. 9876-5432">
                    </div>
                </div>

                <div class="field">
                    <label for="estado_colaborador_id">Estado del colaborador<?= $req(true) ?></label>
                    <div class="select-wrap">
                        <select id="estado_colaborador_id" name="estado_colaborador_id" required>
                            <?php foreach ($catalogos['estados_colaborador'] as $estado): ?>
                                <option value="<?= e($estado['id']) ?>" <?= selected($values['estado_colaborador_id'], $estado['id']) ?>><?= e($estado['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <section class="form-section-card">
            <header class="form-section-card__head">
                <span class="form-section-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </span>
                <h2>Perfil laboral</h2>
            </header>
            <div class="form-section-card__body form-grid">
                <div class="field">
                    <label for="ocupacion_id">Puesto u ocupacion<?= $req(true) ?></label>
                    <div class="select-wrap">
                        <select id="ocupacion_id" name="ocupacion_id" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($catalogos['ocupaciones'] as $ocupacion): ?>
                                <option value="<?= e($ocupacion['id']) ?>" <?= selected($values['ocupacion_id'], $ocupacion['id']) ?>><?= e($ocupacion['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="tipo_empleado_id">Tipo de empleado<?= $req(true) ?></label>
                    <div class="select-wrap">
                        <select id="tipo_empleado_id" name="tipo_empleado_id" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($catalogos['tipos_empleado'] as $tipo): ?>
                                <option value="<?= e($tipo['id']) ?>" <?= selected($values['tipo_empleado_id'], $tipo['id']) ?>><?= e($tipo['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="planilla_id">Planilla<?= $req(true) ?></label>
                    <div class="select-wrap">
                        <select id="planilla_id" name="planilla_id" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($catalogos['tipos_planilla'] as $planilla): ?>
                                <option value="<?= e($planilla['id']) ?>" <?= selected($values['planilla_id'], $planilla['id']) ?>><?= e($planilla['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="departamento_id">Departamento<?= $req(true) ?></label>
                    <div class="select-wrap">
                        <select id="departamento_id" name="departamento_id" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($catalogos['departamentos'] as $dep): ?>
                                <option value="<?= e($dep['id']) ?>" <?= selected($values['departamento_id'], $dep['id']) ?>><?= e($dep['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="salario">Salario<?= $req(true) ?></label>
                    <div class="input-wrap input-wrap--currency">
                        <span class="input-wrap__prefix" aria-hidden="true">L.</span>
                        <input id="salario" type="number" name="salario" min="0.01" step="0.01" required value="<?= e($values['salario']) ?>" placeholder="0.00">
                    </div>
                </div>

                <div class="field">
                    <label for="fecha_inicio">Fecha inicio<?= $req(true) ?></label>
                    <input id="fecha_inicio" type="date" name="fecha_inicio" required value="<?= e($values['fecha_inicio']) ?>">
                </div>

                <div class="field span-2">
                    <label for="fecha_fin">Fecha fin</label>
                    <input id="fecha_fin" type="date" name="fecha_fin" value="<?= e($values['fecha_fin'] ?? '') ?>">
                    <span class="hint">Dejar en blanco si el empleado es actual.</span>
                </div>
            </div>
        </section>

        <footer class="form-page__foot">
            <a class="btn" href="<?= e(url('colaboradores.index')) ?>">Cancelar</a>
            <button class="btn primary" type="submit">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <?= e($button) ?>
            </button>
        </footer>
    </div>
</form>
