# Sistema de Capital Humano

Sistema academico de gestion de Capital Humano desarrollado en PHP nativo con arquitectura MVC. Permite administrar colaboradores, perfiles laborales, promociones, bajas, usuarios, roles, permisos, vacaciones, historial academico y reportes, cumpliendo con los requisitos de seguridad OWASP, principios DRY/SOLID y firmas digitales OpenSSL para garantizar la integridad de datos sensibles como salarios.

Aplicacion web completa para gestion de Capital Humano con autenticacion, control de acceso basado en roles (RBAC), exportacion a Excel y una API REST orientada a la Contraloria General.

---

## Integrantes del Equipo
* **Aaron Ortiz** - Cédula: 8-1029-2487 - Rol: Diseño Funcional y Arquitectura
* **Orlando Valdez** - Cédula: 8-997-1296 - Rol: Diseño Funcional y Arquitectura
* **Gustavo Dominguez** - Cédula: 20-43-7967 - Rol: Diseñador Frontend
* **Andre Reboulet** - Cédula: 8-1026-1624 - Rol: Desarrollador Backend

---

## Fecha del Sistema y Versión
* **Versión actual del software:** `v2.0.0`
* **Fecha:** [15/7/2026]

---

## Demostración en Video
[![Ver Demostración en Video](https://img.shields.io/badge/Video_Demostrativo-Ver_en_YouTube-red?style=for-the-badge&logo=youtube)]([Colocar enlace directo a YouTube o Drive aquí])

> **Nota:** En este enlace directo se encuentra la sustentación del grupo, la ejecución del sistema y la explicación correspondiente.

## Entorno de Ejecución

- PHP 8.1 o superior.
- MySQL o MariaDB.
- PDO con sentencias preparadas.
- Programacion orientada a objetos.
- Arquitectura MVC sencilla.
- OpenSSL para firma y verificacion de datos sensibles.
- Fileinfo para verificar el contenido real de archivos subidos.
- ImageMagick recomendado para generar miniaturas normalizadas de fotografías.
- PhpSpreadsheet para exportar reportes `.xlsx`.
- HTML5, CSS3 y JavaScript basico.

## Instalacion

1. Instalar dependencias:

```bash
composer install
```

2. Copiar `.env.example` a `.env` y ajustar los valores:

```bash
cp .env.example .env
```

El archivo `.env` ya viene configurado para desarrollo local, pero verifica estos valores:

```env
APP_URL=http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CapitalHumano
DB_USERNAME=root
DB_PASSWORD=
```

3. Importar la base en phpMyAdmin:

```text
database/capital_humano.sql
```

Si se actualiza una instalación anterior, ejecutar una sola vez:

```text
database/migrations/20260714_completar_vacaciones.sql
database/migrations/20260714_reincorporacion.sql
```

4. Generar una credencial exclusiva para la API de Contraloría y guardarla en `.env`:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

```env
CONTRALORIA_API_TOKEN=valor_generado
```

5. Dar al usuario del servidor web acceso de solo lectura a la clave privada de firma. En Arch Linux/Apache, por ejemplo:

```bash
setfacl -m u:http:r config/keys/private.pem
setfacl -m u:http:rwx almacenamiento/logs
```

El segundo permiso permite guardar los errores sin hacer público el directorio. No se debe usar `chmod 644` para la clave privada ni `chmod 777` para los logs. En otros servidores, sustituir `http` por el usuario real de PHP/Apache.

6. Abrir:

```text
http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php
```
## Base de datos

El script unificado crea la base `CapitalHumano` desde cero con todas las tablas requeridas por Contexto.md:

```text
database/capital_humano.sql
```

Tablas incluidas:

- `colaboradores`, `perfiles_laborales`, `historial_academico`, `solicitudes_vacaciones`
- `departamentos`, `cat_estados_colaborador`
- `historial_academico`, `solicitudes_vacaciones`
- `usuarios`, `roles`, `permisos`, `usuario_rol`, `rol_permiso`, `bitacora_accesos`
- Catalogos: `cat_ocupaciones`, `cat_tipoempleado`, `cat_tipos_planilla`, `cat_motivos_terminacion`, `cat_estadocivil`


## Credenciales de prueba

| Usuario | Contrasena | Rol            | Decripción |
| ------- | ---------- | -------------- |------------|
| admin   | Admin123!  | Administrador  | Acceso total: usuarios, roles, colaboradores, reportes, vacaciones. |
| consultor   | Consultor123!  | Consultor  | Solo lectura de reportes, No puede descargar los reportes en formato xlxs. |
| operador   | Operador123!  | Operador  | Gestión diaria de colaboradores, vacaciones y reportes. |

## Directrices Técnicas 
### Control de Acceso Seguro
- Las contraseñas deben tener una longitud obligatoria de 8 a 12 caracteres, lo que reduce la vulnerabilidad frente a ataques por fuerza bruta y obliga a crear credenciales más robustas.
- El sistema bloquea automáticamente una cuenta al tercer intento fallido, limitando los accesos no autorizados y protegiendo la sesión frente a ataques de adivinanza.
- Cada intento de acceso, éxito o fallo, queda registrado en la bitácora de logs con información relevante como usuario, IP, fecha y detalle del evento, permitiendo auditoría y trazabilidad.
- En la arquitectura, esta regla se implementa principalmente en los archivos `app/servicios/AuthService.php`, `app/modelos/Usuario.php` y `app/controladores/AuthController.php`.

### Mitigación OWASP y DRY
- Se implementan tokens CSRF en los formularios para evitar solicitudes forjadas desde herramientas externas como Postman o sitios maliciosos, protegiendo acciones sensibles del sistema.
- La arquitectura sigue una separación clara entre controladores, modelos, servicios y vistas, lo que evita duplicación de lógica y facilita el mantenimiento, la escalabilidad y la revisión de seguridad.
- Además, las entradas del usuario se validan y sanitizan antes de procesarse, reduciendo el riesgo de inyecciones y errores de manipulación de datos.
- En la arquitectura, esta regla se refleja en `app/utilidades/Sanitizer.php`, `app/utilidades/Validator.php`, `app/controladores` y las vistas de `app/vistas`.
- Las cargas usan Fileinfo, extensiones generadas por el servidor, límites de dimensiones y miniaturas sin confiar en el MIME o nombre enviados por el navegador.

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
- Esta firma protege la información en reposo contra manipulaciones directas en la base de datos, como cambios no autorizados de salarios, reservas, ventas o equipos en la CMDB.
- Al consultar un registro, el sistema recalcula la firma y la compara con la almacenada; si existe una diferencia, se detecta una posible alteración y se alerta sobre una violación de integridad.
- En la arquitectura, esta regla se implementa en `app/servicios/IntegrityService.php`, `app/modelos/Colaborador.php` y `app/vistas/reportes/index.php`.

## Funcionalidades principales

### Autenticacion y seguridad
- Inicio de sesion con bloqueo automatico despues de 3 intentos fallidos.
- Bitacora de accesos que registra IP, fecha, intento y anomalias.
- Tokens CSRF en todos los formularios.
- Consultas preparadas con PDO para prevenir inyeccion SQL.

### Administracion de usuarios y roles
- CRUD de usuarios administrativos sin eliminacion fisica.
- Roles personalizables con permisos por modulo.
- Un usuario puede tener varios roles.

### Gestion de colaboradores
- Registro completo: nombres y apellidos separados, fecha de nacimiento, fotografia, direccion, telefono, celular, correo y sexo.
- Estados del colaborador: Activo, Vacaciones, Licencia, Incapacidad, Baja.
- Perfil laboral inicial: ocupacion, tipo de empleado, planilla, departamento, salario y fecha de inicio.
- Promociones: crea un nuevo cargo y desactiva el anterior.
- Bajas: guarda fecha fin, motivo, desactiva cargo y empleado.
- Reincorporaciones: abren un nuevo periodo laboral, conservan la baja y los cargos anteriores, y activan nuevamente al colaborador.
- Historial academico con titulos e instituciones, adjuntando archivos PDF.
- Edad calculada automaticamente a partir de la fecha de nacimiento.

### Reportes y estadisticas
- Reporte visual con indicador de integridad OpenSSL (verde/rojo).
- Busqueda por documento, nombre, correo, direccion u ocupacion.
- Filtros por sexo y rango de edad.
- Filtros por salario mínimo, salario máximo y dirección.
- Paginacion en el reporte.
- Estadisticas por sexo y rangos de edad.
- Estadísticas agrupadas por dirección.
- Exportacion a Excel `.xlsx` respetando los filtros activos.

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
- Endpoint para la Contraloria General con cantidad de colaboradores por sexo.

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
├── app/                  # Aplicacion principal (MVC)
│   ├── controladores/
│   ├── modelos/
│   ├── servicios/
│   ├── utilidades/
│   └── vistas/
├── api/                  # API REST
│   └── Controladores/
├── config/               # Configuracion y conexion PDO
├── database/             # Scripts SQL
├── publico/              # Front controller y assets
├── rutas/                # Tabla de rutas de la app
└── bootstrap.php         # Arranque de la aplicacion
```

## Clases principales

| Clase | Responsabilidad |
| --- | --- |
| `Database` | Conexion PDO singleton |
| `Catalogo` | Catalogos de ocupaciones, tipos de empleado, planillas, motivos, departamentos y estados |
| `Colaborador` | CRUD, promociones, bajas, historial, estadisticas y reportes |
| `Vacacion` | Solicitudes y calculo de dias generados |
| `HistorialAcademico` | Registros academicos en PDF |
| `Usuario` | Usuarios, roles y bitacora |
| `Rol` | Roles y permisos |
| `AuthService` | Sesion, login, permisos y bloqueos |
| `IntegrityService` | Firma y verificacion OpenSSL |
| `ExcelExportService` | Genera archivos Excel |
| `FileUploadService` | Subida de fotografias y PDFs |
| `Validator` | Validaciones de entrada |
| `Sanitizer` | Sanitizacion de datos |

## API REST

Endpoint exclusivo para la Contraloría General mediante token Bearer:

```text
GET publico/index.php?route=api.colaboradores.sexo
```

Ejemplo:

```bash
curl -H "Authorization: Bearer VALOR_CONFIGURADO" \
  "http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php?route=api.colaboradores.sexo"
```

Sin token responde `401` en JSON; si la integración no está configurada responde `503`. La API no acepta la sesión administrativa como sustituto de la credencial de Contraloría.

Respuesta de ejemplo:

```json
{
    "fuente": "Sistema de Capital Humano",
    "fecha": "2026-07-07 12:00:00",
    "total_colaboradores": 10,
    "por_sexo": [
        {"sexo": "Masculino", "total": 6},
        {"sexo": "Femenino", "total": 4}
    ]
}
```

## Backup

Para generar un backup de la base de datos ejecuta desde la terminal:

```bash
php database/backup.php
```

El archivo SQL se guarda en `almacenamiento/exports/` con fecha y hora.

## Documentacion

- `docs/uml.md` — diagramas UML (actores, casos de uso, clases, estados, secuencia y DER).
- `docs/diccionario_datos.md` — descripcion de tablas y columnas.
- `docs/manual_usuario.md` — manual con capturas de pantalla (guardar imagenes en `docs/capturas/`).
