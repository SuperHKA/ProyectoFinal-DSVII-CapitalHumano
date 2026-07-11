<?php $appConfig = require ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php'; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? $appConfig['name']) ?> | Sistema de Capital Humano</title>
    <script>
        (function () {
            try {
                var t = localStorage.getItem('ch-theme') || 'light';
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) { document.documentElement.setAttribute('data-theme', 'light'); }
        })();
    </script>
    <link rel="stylesheet" href="<?= e(asset('assets/css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/base.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/components.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/layouts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/dark.css')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script defer src="<?= e(asset('assets/js/theme.js')) ?>"></script>
    <script defer src="<?= e(asset('assets/js/nav.js')) ?>"></script>
    <script defer src="<?= e(asset('assets/js/ui.js')) ?>"></script>
    <script defer src="<?= e(asset('assets/js/charts.js')) ?>"></script>
    <script defer src="<?= e(asset('assets/js/session-timer.js')) ?>"></script>
</head>
<body>
<a class="skip-link" href="#main-content">Saltar al contenido principal</a>

<?php if (\App\Servicios\AuthService::check()): ?>
<div class="app"
     data-session-heartbeat="<?= e(url('session.heartbeat')) ?>"
     data-session-timeout="<?= e((string) \App\Servicios\AuthService::IDLE_TIMEOUT_SECONDS) ?>"
     data-session-seconds-left="<?= e((string) \App\Servicios\AuthService::idleSecondsLeft()) ?>"
     data-session-token="<?= e(\App\Servicios\AuthService::loginToken() ?? '') ?>">
    <?php $currentRoute = $_GET['route'] ?? 'home'; require __DIR__ . DIRECTORY_SEPARATOR . 'sidebar.php'; ?>

    <header class="topbar">
        <div class="flex items-center gap-3">
            <button class="btn btn--ghost btn--icon menu-toggle" data-menu-toggle aria-label="Abrir menu">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="topbar__search">
                <div style="position:relative;">
                    <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input class="input" type="search" placeholder="Buscar..." style="padding-left: 32px; background: var(--surface-2); border-color: transparent; height: 32px;">
                </div>
            </div>
        </div>
        <div class="topbar__right">
            <div class="session-timer" data-session-timer title="Tiempo de inactividad restante" aria-live="polite">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 16 14"/></svg>
                <span data-session-timer-text>--:--</span>
            </div>
            <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema" title="Cambiar tema claro/oscuro">
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </button>
            <div style="width:1px;height:24px;background:var(--border);margin:0 4px;"></div>
            <button class="btn btn--ghost" style="height:32px;padding:0 6px 0 4px;">
                <div class="avatar avatar--sm" aria-hidden="true"><?= e(strtoupper(substr(\App\Servicios\AuthService::user()['nombre'] ?? 'U', 0, 2))) ?></div>
                <span style="font-size:13px;"><?= e(\App\Servicios\AuthService::user()['nombre'] ?? '') ?></span>
            </button>
            <form method="post" action="<?= e(url('logout')) ?>" class="inline" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn--sm btn--ghost">Cerrar sesion</button>
            </form>
        </div>
    </header>

    <main class="main" id="main-content">
        <?php if ($message = flash('success')): ?>
            <div class="alert alert--success"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($message = flash('error')): ?>
            <div class="alert alert--danger"><?= e($message) ?></div>
        <?php endif; ?>
<?php else: ?>
<main class="page" id="main-content">
    <?php if ($message = flash('success')): ?>
        <div class="alert alert--success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert alert--danger"><?= e($message) ?></div>
    <?php endif; ?>
<?php endif; ?>
