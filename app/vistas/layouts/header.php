<?php $appConfig = require ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php'; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? $appConfig['name']) ?> | Sistema de Capital Humano</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(url('home')) ?>">Sistema de Capital Humano</a>
    <nav class="nav">
        <a href="<?= e(url('home')) ?>">HOME</a>
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'ver')): ?>
            <a href="<?= e(url('colaboradores.index')) ?>">Colaboradores</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'crear')): ?>
            <a href="<?= e(url('colaboradores.create')) ?>">Nuevo colaborador</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('reportes', 'ver')): ?>
            <a href="<?= e(url('reportes.index')) ?>">Reporte</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('vacaciones', 'ver')): ?>
            <a href="<?= e(url('vacaciones.index')) ?>">Vacaciones</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('usuarios', 'ver')): ?>
            <a href="<?= e(url('usuarios.index')) ?>">Usuarios</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('roles', 'ver')): ?>
            <a href="<?= e(url('roles.index')) ?>">Roles</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('usuarios', 'ver')): ?>
            <a href="<?= e(url('bitacora')) ?>">Bitacora</a>
        <?php endif; ?>
    </nav>
    <div class="user-menu">
        <?php if (\App\Servicios\AuthService::check()): ?>
            <span class="user-name"><?= e((\App\Servicios\AuthService::user()['nombre'] ?? '')) ?></span>
            <form method="post" action="<?= e(url('logout')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn small">Cerrar sesion</button>
            </form>
        <?php else: ?>
            <a href="<?= e(url('login')) ?>" class="btn small">Iniciar sesion</a>
        <?php endif; ?>
    </div>
</header>
<main class="page">
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert alert-danger"><?= e($message) ?></div>
    <?php endif; ?>
