<?php
$lastEndDate = null;
foreach ($colaborador['perfiles'] as $profile) {
    if (!empty($profile['fecha_fin']) && ($lastEndDate === null || $profile['fecha_fin'] > $lastEndDate)) {
        $lastEndDate = $profile['fecha_fin'];
    }
}
$minimumStartDate = $lastEndDate !== null
    ? (new DateTimeImmutable($lastEndDate))->modify('+1 day')->format('Y-m-d')
    : date('Y-m-d');
$values = array_merge([
    'ocupacion_id' => '',
    'tipo_empleado_id' => '',
    'planilla_id' => '',
    'departamento_id' => '',
    'salario' => '',
    'fecha_inicio' => max(date('Y-m-d'), $minimumStartDate),
    'fecha_fin' => '',
], $old ?? []);
?>
<section class="section-head">
    <h1>Reincorporar colaborador</h1>
    <p><?= e($colaborador['nombre_completo']) ?></p>
</section>

<aside class="rehire-context">
    <span class="rehire-context__mark" aria-hidden="true">↻</span>
    <div>
        <strong>Se conservará todo el historial anterior</strong>
        <p>La reincorporación abrirá un nuevo periodo laboral y cambiará el estado del colaborador a Activo. Última fecha de baja: <?= e($lastEndDate ?? 'No registrada') ?>.</p>
    </div>
</aside>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('colaboradores.reincorporate', ['id' => $colaborador['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>
    <fieldset class="span-2 form-section">
        <legend>Nuevo periodo laboral</legend>
        <?php require __DIR__ . DIRECTORY_SEPARATOR . '_employment_fields.php'; ?>
    </fieldset>
    <div class="form-actions">
        <button class="btn primary" type="submit">Confirmar reincorporación</button>
        <a class="btn ghost" href="<?= e(url('colaboradores.show', ['id' => $colaborador['id']])) ?>">Cancelar</a>
    </div>
</form>
