<?php

declare(strict_types=1);

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

$routes = require ROOT_PATH . DIRECTORY_SEPARATOR . 'rutas' . DIRECTORY_SEPARATOR . 'web.php';
$route = $_GET['route'] ?? 'public.home';

if (!is_string($route) || !isset($routes[$route])) {
    http_response_code(404);
    render_view('errors/generic', [
        'title' => 'Ruta no encontrada',
        'status' => 404,
        'heading' => 'La página solicitada no existe',
        'description' => 'Revise la dirección o vuelva a la página de inicio.',
    ]);
    exit;
}

[$class, $method] = $routes[$route];

try {
    (new $class())->$method();
} catch (Throwable $exception) {
    $status = http_response_code();
    if ($status < 400) {
        $status = 500;
        http_response_code($status);
    }
    if ($status >= 500) {
        (new \App\Servicios\WebErrorHandler())->handle($exception);
    }
    render_view('errors/generic', [
        'title' => $status === 403 ? 'Acceso denegado' : 'Error de aplicación',
        'status' => $status,
        'heading' => $status === 403 ? 'No tiene permiso para esta acción' : 'Ocurrió un error inesperado',
    ]);
}
