<section class="section-head">
    <h1>Nuevo rol</h1>
    <p>Defina los permisos del rol.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('roles.store')) ?>">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
</form>
