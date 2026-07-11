<?php
/**
 * Sidebar del sistema. Se incluye desde app/vistas/layouts/header.php
 * dentro de <aside class="sidebar">. Submenus y ARIA los maneja nav.js.
 *
 * Variables esperadas:
 *   - $currentRoute (string) ruta actual sin prefijo (ej: 'colaboradores.index')
 *   - $currentPrefix (string) primer segmento de la ruta (para marcar item activo)
 */
$currentRoute = $currentRoute ?? ($_GET['route'] ?? 'home');
$currentPrefix = explode('.', (string) $currentRoute)[0];

$is = static fn(string $r): bool => str_starts_with((string) $currentRoute, $r);
?>
<aside class="sidebar" aria-label="Navegacion principal">
  <a class="brand-block" href="<?= e(url('home')) ?>">
    <div class="logo">CH</div>
    <div>
      <div class="name">Capital Humano</div>
      <div class="sub">v2 · interno</div>
    </div>
  </a>

  <nav>
    <a class="nav-link <?= $currentRoute === 'home' || $currentRoute === 'inicio' ? 'is-active' : '' ?>"
       href="<?= e(url('home')) ?>" <?= $currentRoute === 'home' || $currentRoute === 'inicio' ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Inicio
    </a>

    <?php if (\App\Servicios\AuthService::can('colaboradores', 'ver')): ?>
    <div class="dropdown" data-open="<?= $currentPrefix === 'colaboradores' || $currentPrefix === 'vacaciones' ? 'true' : 'false' ?>">
      <button class="dropdown__toggle" aria-haspopup="true" aria-expanded="<?= $currentPrefix === 'colaboradores' || $currentPrefix === 'vacaciones' ? 'true' : 'false' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Colaboradores
        <svg class="dropdown__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
      </button>
      <ul class="dropdown__menu" role="group" aria-label="Colaboradores">
        <li><a href="<?= e(url('colaboradores.index')) ?>" <?= $is('colaboradores.index') || $is('colaboradores.edit') || $is('colaboradores.show') ? 'aria-current="page"' : '' ?>>Listado</a></li>
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'crear')): ?>
          <li><a href="<?= e(url('colaboradores.create')) ?>" <?= $is('colaboradores.create') ? 'aria-current="page"' : '' ?>>Nuevo</a></li>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('vacaciones', 'ver')): ?>
          <li><a href="<?= e(url('vacaciones.index')) ?>" <?= $currentPrefix === 'vacaciones' ? 'aria-current="page"' : '' ?>>Vacaciones</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <?php endif; ?>

    <?php if (\App\Servicios\AuthService::can('reportes', 'ver')): ?>
    <a class="nav-link <?= $currentPrefix === 'reportes' ? 'is-active' : '' ?>"
       href="<?= e(url('reportes.index')) ?>" <?= $currentPrefix === 'reportes' ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Reportes
    </a>
    <?php endif; ?>

    <?php if (\App\Servicios\AuthService::can('usuarios', 'ver') || \App\Servicios\AuthService::can('roles', 'ver')): ?>
    <div class="dropdown" data-open="<?= $currentPrefix === 'usuarios' || $currentPrefix === 'roles' ? 'true' : 'false' ?>">
      <button class="dropdown__toggle" aria-haspopup="true" aria-expanded="<?= $currentPrefix === 'usuarios' || $currentPrefix === 'roles' ? 'true' : 'false' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Admin
        <svg class="dropdown__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
      </button>
      <ul class="dropdown__menu" role="group" aria-label="Administracion">
        <?php if (\App\Servicios\AuthService::can('usuarios', 'ver')): ?>
          <li><a href="<?= e(url('usuarios.index')) ?>" <?= $currentPrefix === 'usuarios' ? 'aria-current="page"' : '' ?>>Usuarios</a></li>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('roles', 'ver')): ?>
          <li><a href="<?= e(url('roles.index')) ?>" <?= $currentPrefix === 'roles' ? 'aria-current="page"' : '' ?>>Roles</a></li>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('usuarios', 'ver')): ?>
          <li><a href="<?= e(url('bitacora')) ?>" <?= $currentRoute === 'bitacora' ? 'aria-current="page"' : '' ?>>Bitacora</a></li>
        <?php endif; ?>
        <li><a href="<?= e(url('admin.index')) ?>" <?= $currentRoute === 'admin.index' ? 'aria-current="page"' : '' ?>>Resumen</a></li>
      </ul>
    </div>
    <?php endif; ?>
  </nav>
</aside>
<div class="sidebar-backdrop" data-open="false" aria-hidden="true"></div>
