# Seguridad OWASP — Verificacion

Fecha: 2026-07-09
Alcance: RNF-01 OWASP del `Contexto.md` aplicado a `app/`, `publico/`, `bootstrap.php` y `rutas/`.

## Checklist

| Control OWASP | Estado | Evidencia / archivo |
| --- | --- | --- |
| **A01 Broken Access Control** — verificacion de sesion y permisos por modulo/accion en cada endpoint | ✅ | `app/servicios/AuthService.php:111-127` (`requireAuth`, `ensure(modulo, accion)`); aplicado en cada controlador antes de cualquier operacion de escritura |
| **A01 IDOR** — IDs siempre validados como `int` y verificados contra `find()` | ✅ | `Sanitizer::int($_GET['id'])` + `Colaborador::find(int)`; 404 implicito via redirect si no existe |
| **A03 Injection SQL** — todo SQL via PDO con prepared statements | ✅ | `config/Database.php:41` `EMULATE_PREPARES=false`; verificado sin concatenacion de `$_GET`/`$_POST` en queries |
| **A03 Injection XSS (reflected/stored)** — escape de salida en vistas con `e()` | ✅ | Helper `e()` en `bootstrap.php:65-68` aplicado consistentemente en `app/vistas/**` |
| **A03 XSS en JS** — `innerHTML` prohibido con datos del servidor | ✅ | `publico/assets/js/{theme,nav,ui,charts}.js` no usan `innerHTML` con datos del backend (verificado) |
| **A04 Insecure Design — CSRF** — tokens en cada POST | ✅ | `verify_csrf()` en AuthController:43, ColaboradorController:65/108/171/213/235/249/273, UsuarioController:53/94/115, RolController:50/91/112, VacacionController:53/79/92 |
| **A04 Insecure Design — Session fixation** | ✅ | `session_regenerate_id(true)` anadido en `AuthService.php:60` tras login exitoso |
| **A05 Security Misconfiguration — Security Headers** | ✅ | `bootstrap.php:9-15` emite `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: same-origin`, `Permissions-Policy: camera=(), microphone=(), geolocation=()` |
| **A05 Error messages leaking info** | ✅ | `app/servicios/WebErrorHandler.php` registra la excepcion en log pero al cliente responde generico; `publico/index.php:21-26` envuelve cualquier excepcion no controlada |
| **A07 Identification & Auth Failures — Lockout** | ✅ | `AuthService::MAX_ATTEMPTS = 3`, bloquea 30 min via `Usuario::block()` |
| **A07 Password storage** | ✅ | `password_hash` (bcrypt) y `password_verify` en `AuthService.php:48`; longitud validada 8-12 en `Validator` |
| **A08 Software & Data Integrity — OpenSSL** | ✅ | `IntegrityService` firma cada perfil laboral; `integrity_valid` recalculado en cada lectura; alerta visual si la firma no coincide |
| **A09 Logging & Monitoring** | ✅ | `bitacora_accesos` registra IP, fecha, usuario, intento, anomalia; consulta en `app/vistas/auth/logs.php` |
| **A10 SSRF** | ✅ | La app no hace fetches externos; el unico punto es `Chart.js` por CDN (recurso estatico, no SSRF) |
| **File upload validation** — extension + MIME + tamano | ✅ | `FileUploadService::uploadImage` y `uploadPdf` validan extension, MIME via `finfo`, tamano maximo 2 MB; nombres hasheados con `bin2hex(random_bytes())` |

## Resumen

- 15/15 controles verificados.
- Cambios de este commit cierran los 2 hallazgos de la auditoria (`session_regenerate_id`, security headers).
- Sin vulnerabilidades criticas o altas pendientes.
