# Sistema de Capital Humano

Sistema académico de gestión de Capital Humano desarrollado en PHP nativo con arquitectura MVC. Permite administrar colaboradores, perfiles laborales, promociones, bajas, usuarios, roles, permisos, vacaciones, historial académico y reportes, cumpliendo con requisitos de seguridad OWASP, principios DRY/SOLID y firmas digitales OpenSSL para garantizar la integridad de datos sensibles como salarios.

Aplicación web completa para gestión de Capital Humano con autenticación, control de acceso basado en roles (RBAC), exportación a Excel y una API REST orientada a la Contraloría General.

---

## Integrantes del Equipo
* **Aaron Ortiz** - Cédula: 8-1029-2487 - Rol: Diseño Funcional y Arquitectura
* **Orlando Valdez** - Cédula: 8-997-1296 - Rol: Diseño Funcional y Arquitectura
* **Gustavo Dominguez** - Cédula: 20-43-7967 - Rol: Diseñador Frontend
* **Andre Reboulet** - Cédula: 8-1026-1624 - Rol: Desarrollador Backend

---

## Fecha del Sistema y Versión
* **Versión actual del software:** `v2.0.0`
* **Fecha:** 15/7/2026

---

## Demostración en Video
[![Ver Demostración en Video](https://img.shields.io/badge/Video_Demostrativo-Ver_en_YouTube-red?style=for-the-badge&logo=youtube)](https://youtu.be/nBS8YmIczCQ)

> **Nota:** En este enlace directo se encuentra la sustentación del grupo, la ejecución del sistema y la explicación correspondiente.

## Entorno de Ejecución

- PHP 8.1 o superior (probado en PHP 8.3).
- Apache mediante WAMP, XAMPP o Laragon.
- MySQL 8 o MariaDB.
- Composer 2.
- PDO con sentencias preparadas.
- Extensiones PHP `mbstring`, `fileinfo`, `openssl` y `pdo_mysql`.
- Extensiones `dom`, `xml`, `xmlreader`, `xmlwriter` y `zip` requeridas por PhpSpreadsheet.
- Programación orientada a objetos y arquitectura MVC.
- OpenSSL para firma y verificación de datos sensibles.
- Fileinfo para verificar el contenido real de archivos subidos.
- ImageMagick es opcional y se usa para generar miniaturas normalizadas de fotografías.
- PhpSpreadsheet para exportar reportes `.xlsx`.
- HTML5, CSS3 y JavaScript.

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/SuperHKA/ProyectoFinal-DSVII-CapitalHumano.git
cd ProyectoFinal-DSVII-CapitalHumano
```

En WAMP también puede clonarse directamente dentro de `C:\wamp64\www`.

### 2. Instalar dependencias y generar el autoload

```bash
composer install
composer dump-autoload --optimize
```

Después de cada `git pull` que agregue clases nuevas, ejecute nuevamente `composer dump-autoload --optimize`. La carpeta `vendor/` no se versiona en Git.

### 3. Crear la configuración local

PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux, macOS o Git Bash:

```bash
cp .env.example .env
```

Edite `.env` y verifique la URL y conexión a la base de datos:

```env
APP_URL=http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CapitalHumano
DB_USERNAME=root
DB_PASSWORD=
```

Si la carpeta del proyecto tiene otro nombre o ubicación, ajuste `APP_URL`.

### 4. Crear la base de datos

Importe desde phpMyAdmin el script unificado:

```text
database/capital_humano.sql
```

El script crea la base `CapitalHumano`, sus tablas, catálogos, roles, permisos y el usuario administrador. Si se actualiza una instalación anterior, ejecute una sola vez y en este orden:

```text
database/migrations/20260714_completar_vacaciones.sql
database/migrations/20260714_reincorporacion.sql
```

### 5. Generar las llaves de firma

Las llaves `.pem` están excluidas de Git. Deben generarse antes de registrar colaboradores o perfiles laborales:

```bash
openssl genrsa -out config/keys/private.pem 2048
openssl rsa -in config/keys/private.pem -pubout -out config/keys/public.pem
```

Si `openssl` no está en el `PATH` de Windows, puede localizar el ejecutable incluido con WAMP desde PowerShell:

```powershell
$openssl = (Get-ChildItem C:\wamp64\bin\apache -Recurse -Filter openssl.exe | Select-Object -First 1).FullName
& $openssl genrsa -out config/keys/private.pem 2048
& $openssl rsa -in config/keys/private.pem -pubout -out config/keys/public.pem
```

También puede configurar rutas externas mediante `SIGNING_PRIVATE_KEY` y `SIGNING_PUBLIC_KEY` en `.env`.

### 6. Configurar el token del API

Genere una credencial exclusiva para la integración de Contraloría:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

```env
CONTRALORIA_API_TOKEN=valor_generado
```

### 7. Verificar permisos de archivos

En Windows/WAMP normalmente no se requiere ningún cambio adicional. Verifique que Apache pueda leer `config/keys/private.pem` y escribir en `almacenamiento/logs`, `almacenamiento/exports` y `publico/uploads`.

En Linux/Apache puede conceder permisos mínimos mediante ACL:

```bash
setfacl -m u:http:r config/keys/private.pem
setfacl -m u:http:rwx almacenamiento/logs
setfacl -m u:http:rwx almacenamiento/exports
setfacl -m u:http:rwx publico/uploads
```

Sustituya `http` por el usuario real de PHP/Apache. No use `chmod 644` para la llave privada ni `chmod 777` para las carpetas de escritura.

### 8. Cargar colaboradores de demostración (opcional)

Después de importar la base y generar las llaves, puede cargar los registros usados para probar reportes, bajas, reincorporaciones y vacaciones:

```bash
php database/seed_demo_collaborators.php
```

### 9. Abrir la aplicación

```text
http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php
```

Si aparece `Class "..." not found` después de clonar o actualizar, ejecute `composer install` y `composer dump-autoload --optimize` desde la raíz del proyecto.

### 10. Verificación rápida

Compruebe las dependencias y la sintaxis de los puntos de entrada:

```bash
composer validate --no-check-publish
composer check-platform-reqs
php -l bootstrap.php
php -l publico/index.php
```

Una instalación correcta debe abrir la portada pública y permitir iniciar sesión con las credenciales indicadas abajo.

## Base de datos

El script unificado crea la base `CapitalHumano` desde cero con todas las tablas requeridas por Contexto.md:

```text
database/capital_humano.sql
```

Tablas incluidas:

- `colaboradores`, `perfiles_laborales`, `historial_academico`, `solicitudes_vacaciones`
- `departamentos`, `cat_estados_colaborador`
- `usuarios`, `roles`, `permisos`, `usuario_rol`, `rol_permiso`, `bitacora_accesos`
- Catálogos: `cat_ocupaciones`, `cat_tipoempleado`, `cat_tipos_planilla`, `cat_motivos_terminacion`, `cat_estadocivil`


## Credenciales de prueba

| Usuario | Contraseña | Rol | Descripción |
| --- | --- | --- | --- |
| `admin` | `Admin123!` | Administrador | Acceso total a usuarios, roles, colaboradores, reportes y vacaciones. |

El script SQL crea únicamente el usuario `admin`. Los roles `Operador` y `Consultor` también quedan disponibles, pero sus usuarios deben crearse desde el módulo **Usuarios**. El Operador administra colaboradores, vacaciones y reportes; el Consultor solo puede visualizar reportes y no puede exportarlos.

## Directrices técnicas
### Control de Acceso Seguro
- Las contraseñas deben tener una longitud obligatoria de 8 a 12 caracteres, lo que reduce la vulnerabilidad frente a ataques por fuerza bruta y obliga a crear credenciales más robustas.
- El sistema bloquea automáticamente una cuenta al tercer intento fallido, limitando los accesos no autorizados y protegiendo la sesión frente a ataques de adivinanza.
- Cada intento de acceso, éxito o fallo, queda registrado en la bitácora de logs con información relevante como usuario, IP, fecha y detalle del evento, permitiendo auditoría y trazabilidad.
- En la arquitectura, esta regla se implementa principalmente en los archivos `app/servicios/AuthService.php`, `app/modelos/Usuario.php` y `app/controladores/AuthController.php`.

### Mitigación OWASP y DRY
- Se implementan tokens CSRF asociados a la sesión para rechazar formularios sensibles que no incluyan una credencial válida.
- La arquitectura sigue una separación clara entre controladores, modelos, servicios y vistas, lo que evita duplicación de lógica y facilita el mantenimiento, la escalabilidad y la revisión de seguridad.
- Además, las entradas del usuario se validan y sanitizan antes de procesarse, reduciendo el riesgo de inyecciones y errores de manipulación de datos.
- En la arquitectura, esta regla se refleja en `app/utilidades/Sanitizer.php`, `app/utilidades/Validator.php`, `app/controladores` y las vistas de `app/vistas`.
- Las cargas admiten imágenes y PDF de hasta 2 MB, usan Fileinfo, extensiones generadas por el servidor, límites de dimensiones y miniaturas sin confiar en el MIME o nombre enviados por el navegador.

### Contrato criptográfico
- `CryptographicServiceInterface` define las operaciones comunes `protect`, `verify` y `algorithm`.
- `PasswordHashService` implementa el contrato para contraseñas y `RsaSignatureService` para firmas RSA-SHA256.
- `IntegrityService` consume la abstracción y no depende directamente de las funciones OpenSSL.

### Registro de errores
- `FileLogger` guarda errores diarios en `almacenamiento/logs/errors-AAAA-MM-DD.log`.
- Cada evento recibe un identificador de incidente y elimina contraseñas, tokens y otros secretos del contexto.
- Los errores fatales y las excepciones web no controladas también se registran sin mostrar trazas al usuario.

### Sello de Integridad
- El backend genera una firma digital OpenSSL cada vez que se guarda un registro crítico, usando información del dato y un par de claves para producir un sello único e inalterable.
- Esta firma protege los perfiles laborales contra manipulaciones directas en la base de datos, especialmente cambios no autorizados de salario, puesto, planilla, departamento o fechas laborales.
- Al consultar un registro, el sistema recalcula la firma y la compara con la almacenada; si existe una diferencia, se detecta una posible alteración y se alerta sobre una violación de integridad.
- En la arquitectura, esta regla se implementa en `app/servicios/IntegrityService.php`, `app/modelos/Colaborador.php` y `app/vistas/reportes/index.php`.

## Funcionalidades principales

### Autenticación y seguridad
- Inicio de sesión con bloqueo automático después de 3 intentos fallidos.
- Bitácora de accesos que registra usuario, IP, fecha, resultado y bloqueos.
- Tokens CSRF en todos los formularios.
- Consultas preparadas con PDO para prevenir inyección SQL.

### Administración de usuarios y roles
- CRUD de usuarios administrativos sin eliminación física.
- Roles personalizables con permisos por modulo.
- Un usuario puede tener varios roles.

### Gestión de colaboradores
- Registro completo: nombres y apellidos separados, fecha de nacimiento, fotografía, dirección, teléfono, celular, correo y sexo.
- Estados del colaborador: Activo, Vacaciones, Licencia, Incapacidad, Baja.
- Perfil laboral inicial: ocupación, tipo de empleado, planilla, departamento, salario y fecha de inicio.
- Promociones: crea un nuevo cargo y desactiva el anterior.
- Bajas: guarda fecha fin, motivo, desactiva cargo y empleado.
- Reincorporaciones: abren un nuevo periodo laboral, conservan la baja y los cargos anteriores, y activan nuevamente al colaborador.
- Historial académico con títulos e instituciones, adjuntando archivos PDF.
- Edad calculada automáticamente a partir de la fecha de nacimiento.

### Reportes y estadísticas
- Reporte visual con indicador de integridad OpenSSL (verde/rojo).
- Busqueda por documento, nombre, correo, direccion u ocupacion.
- Filtros por sexo y rango de edad.
- Filtros por salario mínimo, salario máximo y dirección.
- Paginación en el reporte.
- Estadísticas por sexo y rangos de edad.
- Estadísticas agrupadas por dirección.
- Exportación a Excel `.xlsx` respetando los filtros activos.

### Vacaciones
- Solicitudes de vacaciones con fechas de salida y retorno.
- Cálculo proporcional desde la primera contratación a razón de 1 día por cada 11 días trabajados.
- Control de saldo, solicitudes solapadas y mínimo de 7 días calendario.
- Aprobacion o rechazo de solicitudes.
- Generación del resuelto aprobado en formato PDF.

### Página pública
- Portada pública con beneficios, valor estratégico, módulos, tecnologías y controles de seguridad.
- El acceso inicial no requiere autenticación; los datos operativos continúan protegidos por roles.

### API REST
- Endpoint exclusivo para la Contraloría General con cantidad de colaboradores por sexo.
- Autenticación mediante token Bearer independiente de la sesión administrativa.
- Compatibilidad con encabezados `Authorization` enviados por Apache/WAMP y alternativa `X-API-Key`.

### Interfaz adaptable
- Tablas administrativas con ajuste de columnas y salto de línea para contenido extenso.
- Desplazamiento horizontal de respaldo en dispositivos donde la información no cabe completa.
- Navegación y formularios adaptados a escritorio, tablet y móvil.

## Integridad OpenSSL

Cada perfil laboral se firma con OpenSSL. La firma protege estos datos sensibles:

- identidad
- codigo de empleado
- salario
- tipo de empleado
- planilla
- departamento
- ocupacion
- fecha de inicio

Al consultar un registro se recalcula la firma; si no coincide, el sistema alerta sobre una violacion de integridad. Las llaves se ubican en:

```text
config/keys/private.pem
config/keys/public.pem
```

## Estructura del proyecto

```text
Software7-ProyectoFinal_Version_2/
├── app/                  # Aplicación principal (MVC)
│   ├── contratos/        # Interfaces de criptografía, errores y logging
│   ├── controladores/
│   ├── modelos/
│   ├── servicios/
│   ├── utilidades/
│   └── vistas/
├── api/                  # API REST
│   └── Controladores/
├── almacenamiento/       # Logs, backups y exportaciones generadas
├── config/               # Configuración, conexión PDO y llaves RSA
├── database/             # SQL unificado, migraciones, backup y datos demo
├── docs/                 # Manual, UML y diccionario de datos
├── publico/              # Front controller, assets y archivos subidos
├── rutas/                # Tabla de rutas de la app
├── bootstrap.php         # Arranque y funciones compartidas
├── composer.json         # Dependencias y autoload
└── .env.example          # Plantilla de configuración local
```

## Clases principales

| Clase | Responsabilidad |
| --- | --- |
| `Database` | Conexión PDO singleton |
| `Catalogo` | Catálogos de ocupaciones, tipos de empleado, planillas, motivos, departamentos y estados |
| `Colaborador` | CRUD, promociones, bajas, historial, estadísticas y reportes |
| `Vacacion` | Solicitudes y cálculo de días generados |
| `HistorialAcademico` | Registros académicos en PDF |
| `Usuario` | Usuarios, roles y bitácora |
| `Rol` | Roles y permisos |
| `AuthService` | Sesión, login, permisos y bloqueos |
| `IntegrityService` | Firma y verificación OpenSSL |
| `RsaSignatureService` | Implementa firmas RSA-SHA256 y su verificación |
| `PasswordHashService` | Protege y verifica contraseñas con la API nativa de PHP |
| `ExcelExportService` | Genera archivos Excel |
| `PdfResolutionService` | Genera el resuelto PDF de vacaciones aprobadas |
| `FileUploadService` | Subida de fotografías y PDF |
| `ApiTokenAuthService` | Valida credenciales Bearer y `X-API-Key` del API |
| `FileLogger` | Registra errores y oculta datos sensibles del contexto |
| `WebErrorHandler` | Convierte excepciones web en respuestas seguras |
| `FormState` | Conserva temporalmente datos y errores de formularios |
| `Validator` | Validaciones de entrada |
| `Sanitizer` | Sanitización de datos |

## API REST

Endpoint exclusivo para la Contraloría General mediante token Bearer:

```text
GET publico/index.php?route=api.colaboradores.sexo
```

### Uso con Postman

1. Cree una solicitud `GET` con esta URL:

```text
http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php?route=api.colaboradores.sexo
```

2. En **Authorization**, seleccione **Bearer Token**.
3. Pegue únicamente el valor de `CONTRALORIA_API_TOKEN`; Postman agrega el prefijo `Bearer` automáticamente.
4. Envíe la solicitud. Una credencial válida responde `200` con JSON.

El API también acepta el mismo valor mediante el encabezado `X-API-Key`. Esto es útil como alternativa en servidores proxy que eliminan `Authorization`.

### Uso con cURL

```bash
curl -H "Authorization: Bearer VALOR_CONFIGURADO" \
  "http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php?route=api.colaboradores.sexo"
```

Estados esperados:

| Estado | Significado |
| --- | --- |
| `200` | Credencial válida; devuelve estadísticas por sexo. |
| `401` | Token ausente o incorrecto. |
| `405` | Se utilizó un método diferente de `GET`. |
| `503` | `CONTRALORIA_API_TOKEN` no está configurado. |

La sesión administrativa no sustituye la credencial de Contraloría. No envíe el token en parámetros de URL ni lo publique en Git.

Respuesta de ejemplo:

```json
{
    "fuente": "Sistema de Capital Humano",
    "destinatario": "Contraloria General de la Republica",
    "fecha": "2026-07-15T12:37:06+00:00",
    "total_colaboradores": 11,
    "por_sexo": [
        {"sexo": "Masculino", "total": 6},
        {"sexo": "Femenino", "total": 5}
    ]
}
```

## Backup

Para generar un backup de la base de datos ejecute desde la terminal:

```bash
php database/backup.php
```

El script utiliza la conexión definida en `.env` y guarda el SQL en `almacenamiento/exports/` con fecha y hora. Requiere que `mysqldump` esté disponible en el `PATH`.

En WAMP puede agregar temporalmente la carpeta de MySQL al `PATH` de PowerShell antes de ejecutar el backup:

```powershell
$mysqlBin = (Get-ChildItem C:\wamp64\bin\mysql -Recurse -Filter mysqldump.exe | Select-Object -First 1).DirectoryName
$env:Path = "$mysqlBin;$env:Path"
php database/backup.php
```

## Documentación

- `docs/manual_usuario.md` — instalación y uso de cada módulo con capturas.
- `docs/uml.md` — actores, casos de uso, clases, estados, secuencia y DER en Mermaid.
- `docs/diccionario_datos.md` — descripción de tablas, columnas y relaciones.
- `config/keys/README.md` — generación de llaves OpenSSL.
