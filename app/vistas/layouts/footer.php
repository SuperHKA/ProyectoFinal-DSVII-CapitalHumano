<?php if (\App\Servicios\AuthService::check()): ?>
    </main>
</div>
<?php else: ?>
</main>
<?php endif; ?>
<footer class="app-footer">
    <span>&copy; <?= date('Y') ?> Capital Humano</span>
    <span class="mono">v2.0.0 &middot; PHP <?= PHP_VERSION ?></span>
</footer>
</body>
</html>
