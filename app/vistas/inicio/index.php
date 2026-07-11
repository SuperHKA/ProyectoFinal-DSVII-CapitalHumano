<section class="page-head">
  <div>
    <h1>Panel general</h1>
    <p>Resumen del estado actual de tu equipo y metricas clave.</p>
  </div>
  <div class="page-head__actions">
    <a class="btn" href="<?= e(url('reportes.index')) ?>">Ver reportes</a>
    <?php if (\App\Servicios\AuthService::can('colaboradores', 'crear')): ?>
      <a class="btn btn--accent" href="<?= e(url('colaboradores.create')) ?>">+ Nuevo colaborador</a>
    <?php endif; ?>
  </div>
</section>

<div class="grid grid--auto mb-4">
  <div class="kpi">
    <span class="kpi__label">Colaboradores</span>
    <span class="kpi__value"><?= e($stats['colaboradores']) ?></span>
    <span class="kpi__delta kpi__delta--up">Total registrados</span>
  </div>
  <div class="kpi">
    <span class="kpi__label">Activos</span>
    <span class="kpi__value"><?= e($stats['activos']) ?></span>
    <span class="kpi__delta kpi__delta--up">Con perfil laboral vigente</span>
  </div>
  <div class="kpi">
    <span class="kpi__label">Inactivos / Baja</span>
    <span class="kpi__value"><?= e($stats['colaboradores'] - $stats['activos']) ?></span>
    <span class="muted" style="font-size:12px;">Bajas y licencias</span>
  </div>
  <div class="kpi">
    <span class="kpi__label">% Activos</span>
    <span class="kpi__value"><?= $stats['colaboradores'] > 0 ? round(($stats['activos'] / $stats['colaboradores']) * 100) : 0 ?>%</span>
    <span class="muted" style="font-size:12px;">Del total registrado</span>
  </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
  <section class="chart-card">
    <div>
      <div class="chart-card__title">Distribucion por sexo</div>
      <div class="chart-card__sub">Total de colaboradores registrados</div>
    </div>
    <div class="chart-card__canvas">
      <canvas id="chart-sexo" role="img" aria-label="Grafico de dona con la distribucion por sexo"></canvas>
    </div>
  </section>

  <section class="chart-card">
    <div>
      <div class="chart-card__title">Distribucion por edad</div>
      <div class="chart-card__sub">Rangos 20-25, 25-30, 30-35, mayor de 35</div>
    </div>
    <div class="chart-card__canvas">
      <canvas id="chart-edad" role="img" aria-label="Grafico de barras con la distribucion por rangos de edad"></canvas>
    </div>
  </section>
</div>

<script>
window.addEventListener('load', function () {
  var dataSexo = <?= json_encode($stats['por_sexo'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var dataEdad = <?= json_encode($stats['por_edad'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  if (window.charts) {
    var c1 = document.getElementById('chart-sexo');
    var c2 = document.getElementById('chart-edad');
    if (c1 && dataSexo) window.charts.doughnutSexo(c1, dataSexo);
    if (c2 && dataEdad) window.charts.barEdad(c2, dataEdad);
  }
});
</script>
