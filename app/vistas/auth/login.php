<section class="login-shell">
    <aside class="login-canvas">
        <div class="login-canvas__top">
            <div class="logo">CH</div>
            <div class="name">Capital Humano</div>
        </div>
        <div class="login-canvas__hero">
            <h2>Gestiona tu equipo con confianza.</h2>
            <p>Control de acceso, reportes, vacaciones e integridad de datos sensibles en un solo lugar.</p>
            <ul class="login-canvas__features">
                <li><span class="dot">1</span> RBAC con permisos por modulo y bitacora completa.</li>
                <li><span class="dot">2</span> Firmas digitales OpenSSL para salarios e historial.</li>
                <li><span class="dot">3</span> Exportacion a Excel y API REST para la Contraloria.</li>
            </ul>
        </div>
        <div class="login-canvas__bottom">v2.0.0 &middot; PHP <?= e(PHP_VERSION) ?></div>
    </aside>

    <div class="login-form-side">
        <form class="login-form" method="post" action="<?= e(url('login.process')) ?>" novalidate>
            <h1>Bienvenido</h1>
            <p class="sub">Inicia sesion para continuar</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert--danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="field">
                    <label for="usuario">Usuario</label>
                    <input class="input" type="text" id="usuario" name="usuario" value="<?= e($old['usuario'] ?? '') ?>" autocomplete="username" required autofocus>
                </div>
                <div class="field">
                    <label for="password">Contrasena</label>
                    <div class="input-wrap">
                        <input class="input input--with-icon" type="password" id="password" name="password" autocomplete="current-password" required>
                        <button type="button" class="btn btn--icon btn--ghost icon-btn" data-password-toggle aria-label="Mostrar contrasena">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="grid-column:auto;padding-top:8px;">
                <button type="submit" class="btn btn--primary btn--block">Ingresar</button>
            </div>

            <div class="hint">Demo: <strong>admin</strong> / <strong>Admin123!</strong></div>
        </form>
    </div>
</section>
