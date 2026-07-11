<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Modelos\Usuario;
use DateTimeImmutable;
use RuntimeException;

/**
 * Gestiona sesión, login, permisos y bloqueos.
 */
final class AuthService
{
    private Usuario $usuarios;
    private const MAX_ATTEMPTS = 3;
    public const IDLE_TIMEOUT_SECONDS = 900; // 15 minutos

    public function __construct(?Usuario $usuarios = null)
    {
        $this->usuarios = $usuarios ?? new Usuario();
    }

    public function login(string $usuario, string $password): void
    {
        $row = $this->usuarios->findByUsername($usuario);

        if (!$row) {
            $this->usuarios->logAccess(null, $usuario, 'fallido', true, 'Usuario no existe');
            throw new RuntimeException('Credenciales incorrectas.');
        }

        $id = (int) $row['id'];

        if ((int) $row['activo'] !== 1) {
            $this->usuarios->logAccess($id, $usuario, 'fallido', true, 'Usuario inactivo');
            throw new RuntimeException('Usuario desactivado. Contacte al administrador.');
        }

        if (!empty($row['bloqueado_hasta'])) {
            $blockedUntil = new DateTimeImmutable((string) $row['bloqueado_hasta']);
            if ($blockedUntil > new DateTimeImmutable()) {
                $this->usuarios->logAccess($id, $usuario, 'fallido', true, 'Cuenta bloqueada');
                throw new RuntimeException('Cuenta bloqueada hasta ' . $blockedUntil->format('Y-m-d H:i:s'));
            }
        }

        if (!password_verify($password, (string) $row['password_hash'])) {
            $this->usuarios->incrementFailedAttempts($id);
            $attempts = (int) $row['intentos_fallidos'] + 1;
            $anomalia = $attempts >= self::MAX_ATTEMPTS;
            if ($anomalia) {
                $this->usuarios->block($id);
            }
            $this->usuarios->logAccess($id, $usuario, 'fallido', $anomalia, "Intento {$attempts}");
            throw new RuntimeException('Credenciales incorrectas.');
        }

        $this->usuarios->resetAttempts($id);
        $this->usuarios->logAccess($id, $usuario, 'exitoso', false);

        // OWASP session fixation: regenerar identificador tras autenticacion exitosa.
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['usuario_nombre'] = $row['nombre'];
        $_SESSION['permisos'] = $this->usuarios->permissionIds($id);
        $_SESSION['login_token'] = bin2hex(random_bytes(32));
        $_SESSION['login_token_issued'] = time();
        $_SESSION['last_activity'] = time();
    }

    public function logout(): void
    {
        unset(
            $_SESSION['usuario_id'],
            $_SESSION['usuario'],
            $_SESSION['usuario_nombre'],
            $_SESSION['permisos'],
            $_SESSION['login_token'],
            $_SESSION['login_token_issued'],
            $_SESSION['last_activity']
        );
    }

    public static function loginToken(): ?string
    {
        return $_SESSION['login_token'] ?? null;
    }

    public static function touch(): void
    {
        if (self::check()) {
            $_SESSION['last_activity'] = time();
        }
    }

    public static function checkIdle(): bool
    {
        if (!self::check()) {
            return false;
        }
        $last = (int) ($_SESSION['last_activity'] ?? 0);
        if ($last === 0) {
            $_SESSION['last_activity'] = time();
            return true;
        }
        if ((time() - $last) > self::IDLE_TIMEOUT_SECONDS) {
            return false;
        }
        return true;
    }

    public static function idleSecondsLeft(): int
    {
        $last = (int) ($_SESSION['last_activity'] ?? time());
        $left = self::IDLE_TIMEOUT_SECONDS - (time() - $last);
        return max(0, $left);
    }

    public static function check(): bool
    {
        return !empty($_SESSION['usuario_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id' => $_SESSION['usuario_id'],
            'usuario' => $_SESSION['usuario'],
            'nombre' => $_SESSION['usuario_nombre'],
        ];
    }

    public static function can(string $modulo, string $accion): bool
    {
        $permisos = $_SESSION['permisos'] ?? [];
        if (empty($permisos)) {
            return false;
        }

        // Buscar permiso por modulo+accion. Reutilizamos el modelo Usuario internamente.
        $permisoId = self::permissionId($modulo, $accion);
        return $permisoId !== null && in_array($permisoId, $permisos, true);
    }

    private static function permissionId(string $modulo, string $accion): ?int
    {
        $db = \Config\Database::getConnection();
        $statement = $db->prepare('SELECT id FROM permisos WHERE modulo = :modulo AND accion = :accion');
        $statement->execute(['modulo' => $modulo, 'accion' => $accion]);
        $id = $statement->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    public static function ensure(string $modulo, string $accion): void
    {
        if (!self::check() || !self::checkIdle()) {
            redirect_to('logout');
        }
        if (!self::can($modulo, $accion)) {
            http_response_code(403);
            throw new RuntimeException('No tiene permiso para realizar esta acción.');
        }
    }

    public static function requireAuth(): void
    {
        if (!self::check() || !self::checkIdle()) {
            redirect_to('logout');
        }
        self::touch();
    }
}
