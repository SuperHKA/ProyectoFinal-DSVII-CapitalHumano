<section class="section-head row-between">
    <div>
        <h1>Reporte de colaboradores</h1>
        <p>Perfiles laborales e integridad OpenSSL.</p>
    </div>
    <?php if (\App\Servicios\AuthService::can('reportes', 'exportar')): ?>
        <a class="btn primary" href="<?= e(url('reportes.export', ['q' => $q, 'sexo' => $sexo, 'edad' => $edad, 'direccion' => $direccion, 'salario_min' => $salario_min, 'salario_max' => $salario_max])) ?>">Exportar Excel</a>
    <?php endif; ?>
</section>

<form class="filters" method="get">
    <input type="hidden" name="route" value="reportes.index">
    <input name="q" placeholder="Buscar por documento, nombre, correo, direccion u ocupacion" value="<?= e($q) ?>">
    <select name="sexo">
        <option value="">Todos los sexos</option>
        <?php foreach (['Masculino', 'Femenino', 'Otro'] as $s): ?>
            <option value="<?= e($s) ?>" <?= selected($sexo, $s) ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="edad">
        <option value="">Todas las edades</option>
        <?php foreach (['18-25', '26-35', '36-45', '46-55', '56+'] as $r): ?>
            <option value="<?= e($r) ?>" <?= selected($edad, $r) ?>><?= e($r) ?></option>
        <?php endforeach; ?>
    </select>
    <input name="direccion" placeholder="Dirección o sector" value="<?= e($direccion) ?>">
    <input type="number" name="salario_min" min="0" step="0.01" placeholder="Salario mínimo" value="<?= e($salario_min ?? '') ?>">
    <input type="number" name="salario_max" min="0" step="0.01" placeholder="Salario máximo" value="<?= e($salario_max ?? '') ?>">
    <button class="btn" type="submit">Buscar</button>
    <a class="btn ghost" href="<?= e(url('reportes.index')) ?>">Limpiar</a>
</form>

<section class="stats">
    <h2>Estadisticas</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <strong>Por sexo</strong>
            <?php foreach ($por_sexo as $s): ?>
                <div><?= e($s['sexo']) ?>: <?= e($s['total']) ?></div>
            <?php endforeach; ?>
        </div>
        <div class="stat-card">
            <strong>Por dirección</strong>
            <?php foreach ($por_direccion as $d): ?>
                <div><?= e($d['direccion']) ?>: <?= e($d['total']) ?></div>
            <?php endforeach; ?>
        </div>
        <div class="stat-card">
            <strong>Por rango de edad</strong>
            <?php foreach ($por_edad as $e): ?>
                <div><?= e($e['rango']) ?>: <?= e($e['total']) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="table-wrap report-table">
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Codigo</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Sexo</th>
                <th>Direccion</th>
                <th>Correo</th>
                <th>Celular</th>
                <th>Puesto</th>
                <th>Tipo empleado</th>
                <th>Planilla</th>
                <th>Departamento</th>
                <th>Salario</th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Cargo activo</th>
                <th>Empleado activo</th>
                <th>Motivo</th>
                <th>Integridad</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?php if (!empty($row['fotografia'])): ?>
                        <a href="<?= e($row['fotografia']) ?>" target="_blank" rel="noopener" title="Abrir fotografía original">
                            <img src="<?= e(\App\Servicios\FileUploadService::thumbnailPath($row['fotografia'])) ?>" alt="Foto de <?= e($row['nombre_completo']) ?>" class="report-thumb">
                        </a>
                    <?php else: ?>
                        <span class="muted">Sin foto</span>
                    <?php endif; ?>
                </td>
                <td><?= e($row['codigo_empleado']) ?></td>
                <td><?= e($row['identidad']) ?></td>
                <td><?= e($row['nombre_completo']) ?></td>
                <td><?= e($row['edad'] ?? 'N/A') ?></td>
                <td><?= e($row['sexo']) ?></td>
                <td><?= e($row['direccion'] ?? '') ?></td>
                <td><?= e($row['correo']) ?></td>
                <td><?= e($row['celular']) ?></td>
                <td><?= e($row['ocupacion']) ?></td>
                <td><?= e($row['tipo_empleado']) ?></td>
                <td><?= e($row['planilla']) ?></td>
                <td><?= e($row['departamento'] ?? '') ?></td>
                <td>B/. <?= e(number_format((float) $row['salario'], 2)) ?></td>
                <td><?= e($row['fecha_inicio']) ?></td>
                <td><?= e($row['fecha_fin'] ?? 'N/A') ?></td>
                <td><?= (int) $row['cargo_activo'] === 1 ? 'Si' : 'No' ?></td>
                <td><?= (int) $row['empleado_activo'] === 1 ? 'Si' : 'No' ?></td>
                <td><?= e($row['motivo_baja'] ?? 'No aplica') ?></td>
                <td>
                    <span class="badge <?= !empty($row['integrity_valid']) ? 'ok' : 'danger' ?>">
                        <?= e($row['integrity_message']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
            <tr><td colspan="20" class="empty">No hay registros para el reporte.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if ($paginacion['pages'] > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $paginacion['pages']; $i++): ?>
        <?php if ($i === $paginacion['page']): ?>
            <span class="btn small active"><?= e($i) ?></span>
        <?php else: ?>
            <a class="btn small" href="<?= e(url('reportes.index', ['q' => $q, 'sexo' => $sexo, 'edad' => $edad, 'direccion' => $direccion, 'salario_min' => $salario_min, 'salario_max' => $salario_max, 'page' => $i])) ?>"><?= e($i) ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
<?php endif; ?>
