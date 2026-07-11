<section class="page-head">
  <div>
    <h1>Administracion</h1>
    <p>Resumen de usuarios, roles y actividad reciente del sistema.</p>
  </div>
  <div class="page-head__actions">
    <?php if (\App\Servicios\AuthService::can('usuarios', 'crear')): ?>
      <a class="btn btn--accent" href="<?= e(url('usuarios.create')) ?>">+ Nuevo usuario</a>
    <?php endif; ?>
  </div>
</section>

<div class="grid grid--3 mb-4">
  <div class="kpi">
    <span class="kpi__label">Usuarios totales</span>
    <span class="kpi__value"><?= e($total_users) ?></span>
    <span class="muted" style="font-size:12px;">Cuentas registradas</span>
  </div>
  <div class="kpi">
    <span class="kpi__label">Usuarios activos</span>
    <span class="kpi__value"><?= e($active_users) ?></span>
    <span class="kpi__delta kpi__delta--up">
      <?= $total_users > 0 ? round(($active_users / $total_users) * 100) : 0 ?>% del total
    </span>
  </div>
  <div class="kpi">
    <span class="kpi__label">Roles definidos</span>
    <span class="kpi__value"><?= e($total_roles) ?></span>
    <span class="muted" style="font-size:12px;">Con permisos asignados</span>
  </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
  <section class="card">
    <div class="card--header"><h2>Ultimos accesos</h2></div>
    <div class="table-scroll">
      <table>
        <thead><tr><th>Usuario</th><th>Intento</th><th>IP</th><th>Fecha</th></tr></thead>
        <tbody>
          <?php foreach ($ultimos_accesos as $log): ?>
            <tr>
              <td><?= e($log['usuario'] ?? '-') ?></td>
              <td>
                <span class="badge <?= $log['intento'] === 'exitoso' ? 'badge--ok' : 'badge--danger' ?>">
                  <?= e($log['intento']) ?>
                </span>
              </td>
              <td class="mono"><?= e($log['ip'] ?? '-') ?></td>
              <td class="mono muted" style="font-size:12px;"><?= e($log['fecha'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($ultimos_accesos)): ?>
            <tr><td colspan="4" class="empty">Sin accesos registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="card">
    <div class="card--header"><h2>Roles del sistema</h2>
      <?php if (\App\Servicios\AuthService::can('roles', 'crear')): ?>
        <a class="btn btn--sm" href="<?= e(url('roles.create')) ?>">+ Nuevo</a>
      <?php endif; ?>
    </div>
    <div class="table-scroll">
      <table>
        <thead><tr><th>Nombre</th><th>Estado</th><th>Descripcion</th></tr></thead>
        <tbody>
          <?php foreach ($roles as $rol): ?>
            <tr>
              <td><a href="<?= e(url('roles.edit', ['id' => $rol['id']])) ?>"><?= e($rol['nombre']) ?></a></td>
              <td>
                <span class="badge <?= (int) $rol['activo'] === 1 ? 'badge--ok' : 'badge--warn' ?>">
                  <?= (int) $rol['activo'] === 1 ? 'Activo' : 'Inactivo' ?>
                </span>
              </td>
              <td class="muted" style="font-size:12.5px;"><?= e($rol['descripcion'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
