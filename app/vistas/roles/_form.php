<div class="form-card">
    <div class="form-card__body">
        <div class="form-grid">
            <div class="field span-2">
                <label for="nombre">Nombre del rol</label>
                <input type="text" id="nombre" name="nombre" value="<?= e($old['nombre'] ?? $rol['nombre'] ?? '') ?>" required>
            </div>
            <div class="field span-2">
                <label for="descripcion">Descripcion</label>
                <input type="text" id="descripcion" name="descripcion" value="<?= e($old['descripcion'] ?? $rol['descripcion'] ?? '') ?>" placeholder="Acceso total a todos los modulos">
            </div>
        </div>

        <div class="form-card__section">
            <div class="form-card__section-head">
                <h3>Permisos por modulo</h3>
                <p>Selecciona las acciones que este rol podra realizar.</p>
            </div>
            <div class="permissions-grid">
                <?php foreach ($modulos as $modulo => $permisos): ?>
                    <div class="permission-module">
                        <div class="permission-module__head">
                            <span class="permission-module__name"><?= e(ucfirst($modulo)) ?></span>
                            <span class="permission-module__count"><?= count($permisos) ?></span>
                        </div>
                        <div class="permission-module__items">
                            <?php foreach ($permisos as $permiso): ?>
                                <?php
                                    $checked = '';
                                    $selected = $old['permisos'] ?? $rol['permisos_ids'] ?? [];
                                    if (in_array((int) $permiso['id'], array_map('intval', (array) $selected), true)) {
                                        $checked = 'checked';
                                    }
                                ?>
                                <label class="permission-chip">
                                    <input type="checkbox" name="permisos[]" value="<?= e($permiso['id']) ?>" <?= $checked ?>>
                                    <span><?= e($permiso['accion']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-card__section form-card__section--inline">
            <label class="toggle">
                <input type="checkbox" name="activo" value="1" <?= (int) ($old['activo'] ?? $rol['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
                <span class="toggle__track"><span class="toggle__thumb"></span></span>
                <span class="toggle__label">
                    <strong>Rol activo</strong>
                    <small>Los roles inactivos no pueden asignarse a usuarios.</small>
                </span>
            </label>
        </div>
    </div>
    <div class="form-card__foot">
        <a href="<?= e(url('roles.index')) ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn primary"><?= e($submitLabel ?? 'Guardar') ?></button>
    </div>
</div>
