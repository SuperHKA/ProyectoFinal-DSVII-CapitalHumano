<section class="page-head">
    <div>
        <h1>Reporte de colaboradores</h1>
        <p>Perfiles laborales e integridad OpenSSL.</p>
    </div>
    <div class="page-head__actions">
        <?php if (\App\Servicios\AuthService::can('reportes', 'exportar')): ?>
            <a class="btn btn--accent" href="<?= e(url('reportes.export', ['q' => $q, 'sexo' => $sexo, 'edad' => $edad])) ?>">Exportar Excel</a>
        <?php endif; ?>
    </div>
</section>

<div class="grid mb-4" style="grid-template-columns: 1fr 1fr; gap: 16px;">
    <section class="chart-card">
        <div class="chart-card__title">Distribucion por sexo</div>
        <div class="chart-card__canvas">
            <canvas id="chart-sexo" role="img" aria-label="Grafico de dona con la distribucion por sexo"></canvas>
        </div>
    </section>
    <section class="chart-card">
        <div class="chart-card__title">Distribucion por edad</div>
        <div class="chart-card__sub">Rangos 20-25, 25-30, 30-35, mayor de 35</div>
        <div class="chart-card__canvas">
            <canvas id="chart-edad" role="img" aria-label="Grafico de barras con la distribucion por rangos de edad"></canvas>
        </div>
    </section>
</div>

<form class="filters" method="get">
    <input type="hidden" name="route" value="reportes.index">
    <input class="input" name="q" placeholder="Buscar por documento, nombre, correo, direccion u ocupacion" value="<?= e($q) ?>">
    <select class="select" name="sexo">
        <option value="">Todos los sexos</option>
        <?php foreach (['Masculino', 'Femenino', 'Otro'] as $s): ?>
            <option value="<?= e($s) ?>" <?= selected($sexo, $s) ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <select class="select" name="edad">
        <option value="">Todas las edades</option>
        <?php foreach (['20-25', '25-30', '30-35', 'mayor-35'] as $r): ?>
            <option value="<?= e($r) ?>" <?= selected($edad, $r) ?>><?= e($r) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Buscar</button>
    <a class="btn btn--ghost" href="<?= e(url('reportes.index')) ?>">Limpiar</a>
</form>

<section class="card mb-4">
    <div class="card--header"><h2>Resumen</h2></div>
    <div class="stats-grid">
        <div class="stat-card">
            <strong>Por sexo</strong>
            <?php foreach ($por_sexo as $s): ?>
                <div><span><?= e($s['sexo']) ?></span><span class="num"><?= e($s['total']) ?></span></div>
            <?php endforeach; ?>
        </div>
        <div class="stat-card">
            <strong>Por rango de edad</strong>
            <?php foreach ($por_edad as $r): ?>
                <div><span><?= e($r['rango']) ?></span><span class="num"><?= e($r['total']) ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
window.addEventListener('load', function () {
    if (!window.charts) return;
    var c1 = document.getElementById('chart-sexo');
    var c2 = document.getElementById('chart-edad');
    if (c1) window.charts.doughnutSexo(c1, <?= json_encode($por_sexo, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
    if (c2) window.charts.barEdad(c2, <?= json_encode($por_edad, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
});
</script>

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
                        <img src="<?= e($row['fotografia']) ?>" alt="Foto" class="report-thumb">
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
            <a class="btn small" href="<?= e(url('reportes.index', ['q' => $q, 'sexo' => $sexo, 'edad' => $edad, 'page' => $i])) ?>"><?= e($i) ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
<?php endif; ?>

