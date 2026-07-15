<section class="error-state" role="alert">
    <span class="error-code"><?= e($status ?? 500) ?></span>
    <h1><?= e($heading ?? 'No fue posible completar la solicitud') ?></h1>
    <p><?= e($description ?? 'Puede volver al inicio o intentar nuevamente dentro de unos minutos.') ?></p>
    <a class="btn primary" href="<?= e(\App\Servicios\AuthService::check() ? url('home') : url('public.home')) ?>">Volver al inicio</a>
</section>
