<?php
/**
 * Punto de entrada raiz: delega en el front controller.
 * Se usa cuando el .htaccess no esta disponible o se accede via
 * http://localhost/Software7/.../index.php directamente.
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'publico' . DIRECTORY_SEPARATOR . 'index.php';
