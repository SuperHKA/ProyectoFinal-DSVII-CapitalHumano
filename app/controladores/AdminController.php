<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Rol;
use App\Modelos\Usuario;
use App\Servicios\AuthService;

/**
 * Sirve la vista de resumen administrativo (Admin > Resumen).
 */
final class AdminController
{
    private Usuario $usuarios;
    private Rol $roles;

    public function __construct()
    {
        $this->usuarios = new Usuario();
        $this->roles = new Rol();
    }

    public function index(): void
    {
        AuthService::requireAuth();

        $usuarios = $this->usuarios->all();
        $roles = $this->roles->all();

        render_view('admin/index', [
            'title'       => 'Administracion',
            'usuarios'    => $usuarios,
            'roles'       => $roles,
            'total_users' => count($usuarios),
            'active_users' => count(array_filter($usuarios, static fn(array $u): bool => (int) ($u['activo'] ?? 0) === 1)),
            'total_roles' => count($roles),
            'ultimos_accesos' => $this->usuarios->accessLogs(10),
        ]);
    }
}
