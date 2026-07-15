<?php
$values = array_merge([
    'ocupacion_id' => '',
    'tipo_empleado_id' => '',
    'planilla_id' => '',
    'departamento_id' => '',
    'salario' => '',
    'fecha_inicio' => date('Y-m-d'),
    'fecha_fin' => '',
], $old ?? []);
?>
<section class="section-head">
    <h1>Registrar promocion</h1>
    <p><?= e($colaborador['nombre_completo']) ?></p>
</section>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>
<form method="post" action="<?= e(url('colaboradores.promote', ['id' => $colaborador['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>
    <fieldset class="span-2 form-section">
        <legend>Nuevo perfil laboral</legend>
        <?php require __DIR__ . DIRECTORY_SEPARATOR . '_employment_fields.php'; ?>
    </fieldset>
    <div class="form-actions">
        <button class="btn primary" type="submit">Guardar promocion</button>
        <a class="btn ghost" href="<?= e(url('colaboradores.show', ['id' => $colaborador['id']])) ?>">Cancelar</a>
    </div>
</form>
