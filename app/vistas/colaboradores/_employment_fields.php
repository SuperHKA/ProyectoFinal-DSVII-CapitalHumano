<label>Puesto u ocupacion
    <select name="ocupacion_id" required>
        <option value="">Seleccione</option>
        <?php foreach ($catalogos['ocupaciones'] as $ocupacion): ?>
            <option value="<?= e($ocupacion['id']) ?>" <?= selected($values['ocupacion_id'], $ocupacion['id']) ?>><?= e($ocupacion['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label>Tipo de empleado
    <select name="tipo_empleado_id" required>
        <option value="">Seleccione</option>
        <?php foreach ($catalogos['tipos_empleado'] as $tipo): ?>
            <option value="<?= e($tipo['id']) ?>" <?= selected($values['tipo_empleado_id'], $tipo['id']) ?>><?= e($tipo['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label>Planilla
    <select name="planilla_id" required>
        <option value="">Seleccione</option>
        <?php foreach ($catalogos['tipos_planilla'] as $planilla): ?>
            <option value="<?= e($planilla['id']) ?>" <?= selected($values['planilla_id'], $planilla['id']) ?>><?= e($planilla['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label>Departamento
    <select name="departamento_id">
        <option value="">Sin asignar</option>
        <?php foreach ($catalogos['departamentos'] as $departamento): ?>
            <option value="<?= e($departamento['id']) ?>" <?= selected($values['departamento_id'], $departamento['id']) ?>><?= e($departamento['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label>Salario
    <input type="number" name="salario" min="0.01" step="0.01" required value="<?= e($values['salario']) ?>">
</label>
<label>Fecha inicio
    <input type="date" name="fecha_inicio" required value="<?= e($values['fecha_inicio']) ?>"<?= !empty($minimumStartDate) ? ' min="' . e($minimumStartDate) . '"' : '' ?>>
</label>
