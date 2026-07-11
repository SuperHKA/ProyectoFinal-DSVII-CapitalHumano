<?php

declare(strict_types=1);

use App\Utilidades\Sanitizer;

define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'publico');

// OWASP: cabeceras de seguridad basicas. No aplicar si la peticion ya las emitio
// (tests, CLI o integraciones que inyectan headers por su cuenta).
if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$envFile = ROOT_PATH . DIRECTORY_SEPARATOR . '.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = trim($value, "\"'");
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Lee una variable de entorno con valor por defecto.
 */
function env_value(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

/**
 * Registra una excepcion en almacenamiento/logs/app.log para depuracion.
 */
function log_exception(Throwable $exception): void
{
    $logDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'almacenamiento' . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir) && !mkdir($logDir, 0755, true) && !is_dir($logDir)) {
        return;
    }

    $entry = sprintf(
        "[%s] %s in %s:%d
%s

",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    error_log($entry, 3, $logDir . DIRECTORY_SEPARATOR . 'app.log');
}

/**
 * Escapa salida HTML.
 */
function e(mixed $value): string
{
    return Sanitizer::escape($value);
}

/**
 * Genera una URL absoluta hacia el front controller. Usar siempre
 * rutas con esta funcion; nunca concatenar 'index.php' a mano.
 */
function url(string $route, array $params = []): string
{
    $params = array_merge(['route' => $route], $params);
    $base = base_path();
    $prefix = $base === '' ? '' : '/' . $base;
    return $prefix . '/index.php?' . http_build_query($params);
}

/**
 * Devuelve el subdirectorio donde esta el front controller (sin /index.php).
 * Se calcula desde SCRIPT_NAME (la fuente de verdad en runtime) en vez de
 * parsear APP_URL, para evitar duplicaciones de "publico/" cuando el
 * servidor expone el sitio en una subcarpeta.
 */
function base_path(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if ($script === '') {
        return '';
    }
    $dir = trim(str_replace('\\', '/', dirname($script)), '/');
    return $dir;
}

/**
 * Lee una clave de configuracion.
 */
function config(string $key, mixed $default = null): mixed
{
    static $cache = null;
    if ($cache === null) {
        $cacheFile = ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        $cache = is_file($cacheFile) ? require $cacheFile : [];
    }
    return $cache[$key] ?? $default;
}

/**
 * Genera la URL a un asset estatico. Devuelve siempre una URL absoluta
 * (con "/" al inicio) para que funcione independientemente de la
 * ubicacion actual del navegador.
 */
function asset(string $path): string
{
    $base = base_path();
    $p = ltrim($path, '/');
    if ($base === '') {
        return '/' . $p;
    }
    return '/' . $base . '/' . $p;
}

/**
 * Redirige con Post/Redirect/Get.
 */
function redirect_to(string $route, array $params = []): never
{
    header('Location: ' . url($route, $params));
    exit;
}

/**
 * Renderiza una vista dentro del layout principal.
 *
 * @param array<string,mixed> $data
 */
function render_view(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $viewFile = ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vistas' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
    require ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vistas' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'header.php';
    require $viewFile;
    require ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vistas' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'footer.php';
}

/**
 * Obtiene o define un mensaje flash.
 */
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

/**
 * Genera el token CSRF actual.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Imprime el input CSRF.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Valida el token CSRF de una peticion POST.
 */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        throw new RuntimeException('La sesion expiro o el formulario no es valido. Intente nuevamente.');
    }
}

/**
 * Marca una opcion select como elegida.
 */
function selected(mixed $current, mixed $expected): string
{
    return (string) $current === (string) $expected ? 'selected' : '';
}
