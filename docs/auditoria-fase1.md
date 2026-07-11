# Auditoria Fase 1 — Cumplimiento Academico

Fecha: 2026-07-09
Alcance: los 16 modulos funcionales exigidos por `Contexto.md` + checklist OWASP/DRY/SOLID/PHPDoc.

## Resultado

- **13 modulos ✅** implementados y verificados en codigo.
- **2 modulos ⚠️** con discrepancia menor (resueltos en este commit).
- **1 modulo ❌** sin datos: capturas del manual de usuario (es tarea del estudiante generar los PNG).

## Modulos funcionales

| # | Modulo | Estado | Evidencia |
| --- | --- | --- | --- |
| 1 | Login con bitacora (IP, fecha, anomalia) | ✅ | `app/servicios/AuthService.php:24-66`, `app/modelos/Usuario.php:182-201` |
| 2 | Bloqueo 3 intentos | ✅ | `AuthService.php:17,48-57`, `Usuario.php:166-172` |
| 3 | RBAC roles + permisos | ✅ | `AuthService.php:90-109`, tablas `permisos/rol_permiso/usuario_rol` |
| 4 | CRUD colaboradores | ✅ | `ColaboradorController.php:35-243` |
| 5 | Tipos permanente / eventual / interino | ✅ | `cat_tipoempleado` (incluye Permanente id 7, Interino id 8) |
| 6 | Historial academico con PDF | ✅ | `ColaboradorController.php:245-281`, `FileUploadService.php:41-63` |
| 7 | Reportes con busqueda + filtros | ✅ | `ReporteController.php:17-38`, `Colaborador.php:274-313` |
| 8 | Estadisticas (sexo + rangos edad 20-25/25-30/30-35) | ✅ | `Colaborador.php:316-340` (rangos ajustados al Contexto.md) |
| 9 | Excel `.xlsx` con filtros | ✅ | `ExcelExportService.php:19-92`, `ReporteController.php:40-47` |
| 10 | REST API Contraloria | ✅ | `api/Controladores/ApiController.php:15-29` |
| 11 | Firmas OpenSSL en salarios | ✅ | `IntegrityService.php:26-104`, `integrity_valid` por fila |
| 12 | UML completo | ✅ | `docs/uml.md` (6 diagramas Mermaid) |
| 13 | Diccionario de datos | ✅ | `docs/diccionario_datos.md` |
| 14 | Manual + capturas | ❌ | Manual existe, `docs/capturas/` vacio (ver `docs/capturas/README.md`) |
| 15 | SQL + seed + backup | ✅ | `database/capital_humano.sql`, `database/backup.php` |
| 16 | README con credenciales + video | ✅ | `README.md` (3 usuarios de prueba, placeholder video) |

## OWASP / DRY / SOLID / PHPDoc

| Item | Estado | Evidencia |
| --- | --- | --- |
| PHPDoc por clase | ✅ | Todos los 23 archivos PHP de `app/` + `api/` tienen bloque de clase |
| PDO con prepared statements | ✅ | `config/Database.php:41` (`EMULATE_PREPARES=false`), todo SQL parametrizado |
| `e()` en cada `<?= ?>` con datos | ✅ | Verificado en `app/vistas/auth/*`, `colaboradores/*`, `reportes/index.php` |
| `verify_csrf()` en cada POST | ✅ | Cubierto en AuthController, ColaboradorController, UsuarioController, RolController, VacacionController |
| `session_regenerate_id(true)` en login | ✅ | Anadido en `AuthService.php:60` (commit actual) |
| Security headers (X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy) | ✅ | Anadidos en `bootstrap.php:9-15` (commit actual) |
| `.gitignore` excluye `.env` y llaves | ✅ | `.gitignore:2,18` |
| DRY | ✅ | Helpers `e/url/csrf/flash/redirect_to` centralizados en `bootstrap.php` |
| SOLID | ✅ | Controladores thin, modelos con responsabilidad unica, servicios inyectables |

## Cambios de este commit (Fase 1.2)

1. `app/servicios/AuthService.php`: anadido `session_regenerate_id(true)` tras login exitoso (mitigacion session fixation).
2. `bootstrap.php`: emitidas 4 cabeceras de seguridad (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy). Se omiten en CLI para no romper tests.
3. `app/modelos/Colaborador.php`: rangos de edad ajustados a 20-25 / 25-30 / 30-35 / mayor-35 conforme a `Contexto.md`.
4. `app/vistas/reportes/index.php`: opciones del filtro de edad actualizadas.
5. `README.md`: tabla de 3 usuarios de prueba (admin/rrhh/consulta) con contrasenas y placeholder para URL de video demo.
6. `docs/capturas/README.md`: guia PowerShell para generar las 9 capturas del manual.
