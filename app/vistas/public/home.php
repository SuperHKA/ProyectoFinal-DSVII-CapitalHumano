<div class="public-landing">
    <section class="public-hero">
        <div class="hero-copy public-reveal slide-left">
            <span class="public-kicker">Tecnología con propósito humano</span>
            <h1>Las personas no son registros. Son el motor de cada resultado.</h1>
            <p class="hero-lead">Capital Humano reúne trayectoria, seguridad y decisiones en una plataforma diseñada para cuidar el dato y potenciar el talento.</p>
            <div class="hero-actions">
                <a class="public-btn primary" href="<?= e(url('login')) ?>">Ingresar al sistema <span aria-hidden="true">→</span></a>
                <a class="public-btn secondary" href="#beneficios">Conocer beneficios</a>
            </div>
            <div class="trust-line" aria-label="Características principales">
                <span>Firma RSA</span>
                <span>Acceso por roles</span>
                <span>Reportes verificables</span>
            </div>
        </div>

        <div class="hero-board public-reveal slide-right" aria-label="Resumen visual del sistema">
            <div class="board-top">
                <span>Panorama organizacional</span>
                <span class="live-dot">Datos protegidos</span>
            </div>
            <div class="people-pulse">
                <div class="pulse-number">360°</div>
                <p>Visión integral del ciclo laboral</p>
            </div>
            <div class="board-grid">
                <article><strong>01</strong><span>Identidad y trayectoria</span></article>
                <article><strong>02</strong><span>Movimientos firmados</span></article>
                <article><strong>03</strong><span>Decisiones con evidencia</span></article>
            </div>
            <div class="board-orbit" aria-hidden="true"></div>
        </div>
    </section>

    <section class="public-manifesto public-reveal slide-up" id="beneficios">
        <p>Un sistema de recursos humanos debe hacer más que almacenar.</p>
        <h2>Debe convertir información confiable en mejores decisiones.</h2>
    </section>

    <section class="benefit-grid">
        <article class="benefit-card public-reveal slide-left">
            <span class="card-index">01</span>
            <h3>Trayectoria completa</h3>
            <p>Centraliza perfiles, cargos, promociones, historial académico, estados y vacaciones sin perder el contexto de cada persona.</p>
        </article>
        <article class="benefit-card featured public-reveal slide-up">
            <span class="card-index">02</span>
            <h3>Confianza verificable</h3>
            <p>Los datos laborales sensibles se firman con RSA y se verifican al consultar, haciendo visible cualquier alteración.</p>
        </article>
        <article class="benefit-card public-reveal slide-right">
            <span class="card-index">03</span>
            <h3>Gestión con evidencia</h3>
            <p>Filtros, estadísticas y exportaciones Excel convierten el registro cotidiano en información útil para gestión y control.</p>
        </article>
    </section>

    <section class="public-system" id="tecnologia">
        <div class="system-heading public-reveal slide-left">
            <span class="public-kicker">Arquitectura consciente</span>
            <h2>Seguridad en cada capa, claridad en cada módulo.</h2>
        </div>
        <div class="system-stack public-reveal slide-right">
            <div><span>Aplicación</span><strong>PHP 8 · MVC nativo</strong></div>
            <div><span>Datos</span><strong>MariaDB · PDO preparado</strong></div>
            <div><span>Integridad</span><strong>OpenSSL · RSA-SHA256</strong></div>
            <div><span>Intercambio</span><strong>Excel · API protegida</strong></div>
        </div>
    </section>

    <section class="security-story public-reveal slide-up" id="seguridad">
        <div class="security-mark" aria-hidden="true">✓</div>
        <div>
            <span class="public-kicker">Diseñado para responder</span>
            <h2>Quién accedió. Qué puede hacer. Si el dato sigue íntegro.</h2>
        </div>
        <ul>
            <li>Permisos granulares por rol y módulo</li>
            <li>Bloqueo y registro de intentos de acceso</li>
            <li>Tokens CSRF y validación del lado servidor</li>
            <li>Firma digital de información laboral crítica</li>
        </ul>
    </section>

    <section class="public-cta public-reveal slide-up">
        <div>
            <span class="public-kicker">Capital Humano</span>
            <h2>Información que protege. Gestión que avanza.</h2>
        </div>
        <a class="public-btn light" href="<?= e(url('login')) ?>">Acceder al portal <span aria-hidden="true">→</span></a>
    </section>
</div>
