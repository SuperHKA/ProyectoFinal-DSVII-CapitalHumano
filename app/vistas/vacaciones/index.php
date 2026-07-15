<section class="section-head row-between">
    <div>
        <h1>Solicitudes de vacaciones</h1>
        <p>Gestion de dias generados y solicitados.</p>
    </div>
    <?php if (\App\Servicios\AuthService::can('vacaciones', 'crear')): ?>
        <a class="btn primary" href="<?= e(url('vacaciones.create')) ?>">Nueva solicitud</a>
    <?php endif; ?>
</section>

<section class="vacation-stats" aria-label="Resumen de solicitudes">
    <article><span>Pendientes</span><strong><?= e($resumen['Pendiente']) ?></strong></article>
    <article><span>Aprobadas</span><strong><?= e($resumen['Aprobada']) ?></strong></article>
    <article><span>Rechazadas</span><strong><?= e($resumen['Rechazada']) ?></strong></article>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Colaborador</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Dias solicitados</th>
                <th>Dias generados</th>
                <th>Saldo previo</th>
                <th>Estado</th>
                <th>Resuelto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($solicitudes as $s): ?>
            <tr>
                <td><?= e($s['colaborador']) ?></td>
                <td><?= e($s['fecha_inicio']) ?></td>
                <td><?= e($s['fecha_fin']) ?></td>
                <td><?= e($s['dias_solicitados']) ?></td>
                <td><?= e($s['dias_generados']) ?></td>
                <td><?= e($s['saldo_antes']) ?></td>
                <td>
                    <span class="badge <?= $s['estado'] === 'Aprobada' ? 'ok' : ($s['estado'] === 'Rechazada' ? 'danger' : '') ?>">
                        <?= e($s['estado']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($s['estado'] === 'Aprobada' && !empty($s['numero_resuelto'])): ?>
                        <a class="action-btn view" href="<?= e(url('vacaciones.resuelto', ['id' => $s['id']])) ?>">Descargar PDF</a>
                    <?php else: ?>
                        <span class="muted">No disponible</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <?php if (\App\Servicios\AuthService::can('vacaciones', 'crear') && $s['estado'] === 'Pendiente'): ?>
                        <form method="post" action="<?= e(url('vacaciones.aprobar', ['id' => $s['id']])) ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn ok">Aprobar</button>
                        </form>
                        <form method="post" action="<?= e(url('vacaciones.rechazar', ['id' => $s['id']])) ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn danger-soft">Rechazar</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($solicitudes)): ?>
            <tr><td colspan="9" class="empty">No hay solicitudes.</td></tr>
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
            <a class="btn small" href="<?= e(url('vacaciones.index', ['page' => $i])) ?>"><?= e($i) ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
<?php endif; ?>
