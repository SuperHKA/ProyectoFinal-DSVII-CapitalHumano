<div class="row-two">
    <div class="field">
        <label for="nombre">Nombre del rol</label>
        <input type="text" id="nombre" name="nombre" value="<?= e($old['nombre'] ?? $rol['nombre'] ?? '') ?>" required>
    </div>
    <div class="field">
        <label for="descripcion">Descripción</label>
        <input type="text" id="descripcion" name="descripcion" value="<?= e($old['descripcion'] ?? $rol['descripcion'] ?? '') ?>">
    </div>
</div>
<div class="field">
    <div class="field-heading">
        <label>Permisos por módulo</label>
        <small>Seleccione únicamente las acciones necesarias para este rol.</small>
    </div>
    <div class="permissions-grid">
    <?php foreach ($modulos as $modulo => $permisos): ?>
        <div class="permission-module">
            <header>
                <strong><?= e(ucfirst($modulo)) ?></strong>
                <span><?= e(count($permisos)) ?> acciones</span>
            </header>
            <?php foreach ($permisos as $permiso): ?>
                <?php
                    $checked = '';
                    $selected = $old['permisos'] ?? $rol['permisos_ids'] ?? [];
                    if (in_array((int) $permiso['id'], array_map('intval', (array) $selected), true)) {
                        $checked = 'checked';
                    }
                ?>
                <label class="checkbox">
                    <input type="checkbox" name="permisos[]" value="<?= e($permiso['id']) ?>" <?= $checked ?>>
                    <?= e($permiso['accion']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
<div class="field inline">
    <label class="checkbox">
        <input type="checkbox" name="activo" value="1" <?= (int) ($old['activo'] ?? $rol['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
        <span><strong>Rol activo</strong><small>Sus permisos se aplicarán a los usuarios que lo tengan asignado.</small></span>
    </label>
</div>
