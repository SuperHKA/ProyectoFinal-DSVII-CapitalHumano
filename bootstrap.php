<?php

declare(strict_types=1);

use App\Utilidades\Sanitizer;
use App\Servicios\FileLogger;

define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'publico');

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
 * Registra una excepcion en almacenamiento/logs con un identificador unico.
 *
 * @param array<string,mixed> $context
 */
function log_exception(Throwable $exception, array $context = []): string
{
    return (new FileLogger())->error($exception, $context);
}

/**
 * Registra el detalle y devuelve un mensaje seguro para el usuario.
 *
 * @param array<string,mixed> $context
 */
function safe_error_message(Throwable $exception, string $message = 'No fue posible completar la operacion.', array $context = []): string
{
    return $message . ' Referencia: ' . log_exception($exception, $context) . '.';
}

/**
 * Escapa salida HTML.
 */
function e(mixed $value): string
{
    return Sanitizer::escape($value);
}

/**
 * Genera una URL relativa hacia el front controller.
 */
function url(string $route, array $params = []): string
{
    $params = array_merge(['route' => $route], $params);
    return 'index.php?' . http_build_query($params);
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
 * Restringe acciones mutables al metodo POST.
 */
function require_post(): void
{
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
        http_response_code(405);
        throw new RuntimeException('Metodo HTTP no permitido.');
    }
}

/**
 * Aplica de forma uniforme las protecciones de toda accion mutable.
 */
function require_secure_post(): void
{
    require_post();
    verify_csrf();
}

/**
 * Conserva los datos y vuelve al formulario cuando la validacion falla.
 *
 * @param array<string,mixed> $data
 * @param array<int,string> $errors
 * @param array<string,int|string> $params
 */
function redirect_if_invalid(array $data, array $errors, string $route, array $params = []): void
{
    if ($errors === []) {
        return;
    }

    \App\Utilidades\FormState::flash($data, $errors);
    redirect_to($route, $params);
}

/**
 * Marca una opcion select como elegida.
 */
function selected(mixed $current, mixed $expected): string
{
    return (string) $current === (string) $expected ? 'selected' : '';
}

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) === 0) {
        return false;
    }
    (new FileLogger())->error($message, [
        'severity' => $severity,
        'source_file' => $file,
        'source_line' => $line,
    ]);
    return true;
});

register_shutdown_function(static function (): void {
    $error = error_get_last();
    if ($error === null || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        return;
    }
    (new FileLogger())->error((string) $error['message'], [
        'severity' => $error['type'],
        'source_file' => $error['file'],
        'source_line' => $error['line'],
        'fatal' => true,
    ]);
});
