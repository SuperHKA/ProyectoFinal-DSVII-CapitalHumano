# Sistema de Capital Humano

Sistema academico de gestion de Capital Humano desarrollado en PHP nativo con arquitectura MVC. Permite administrar colaboradores, perfiles laborales, promociones, bajas, usuarios, roles, permisos, vacaciones, historial academico y reportes, cumpliendo con los requisitos de seguridad OWASP, principios DRY/SOLID y firmas digitales OpenSSL para garantizar la integridad de datos sensibles como salarios.

Aplicacion web completa para gestion de Capital Humano con autenticacion, control de acceso basado en roles (RBAC), exportacion a Excel y una API REST orientada a la Contraloria General.

## Tecnologias

- PHP 8.1 o superior.
- MySQL o MariaDB.
- PDO con sentencias preparadas.
- Programacion orientada a objetos.
- Arquitectura MVC sencilla.
- OpenSSL para firma y verificacion de datos sensibles.
- PhpSpreadsheet para exportar reportes `.xlsx`.
- HTML5, CSS3 y JavaScript basico.

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
- Catalogos: `cat_ocupaciones`, `cat_tipoempleado`, `cat_tipos_planilla`, `cat_motivos_terminacion`

No incluye tablas del modulo anterior de inscripciones (`inscriptores` e `inscriptor_temas`) ni catalogos no solicitados como paises, tipos de sangre o rutas.

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

4. Abrir:

```text
http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php
```

## Credenciales de prueba

| Usuario   | Contrasena    | Rol           | Notas |
| --------- | ------------- | ------------- | ----- |
| admin     | Admin123!     | Administrador | Acceso total |
| rrhh      | Rrhh12345!    | RRHH          | Gestion de colaboradores, vacaciones y reportes (sin administracion de usuarios) |
| consulta  | Consulta123!  | Consulta      | Solo lectura: colaboradores, vacaciones, reportes, bitacora |

> Si los usuarios `rrhh` o `consulta` no existen, ejecutar `database/seed_usuarios_prueba.sql` (incluye los 3 usuarios y asigna los roles definidos en el script unificado).

## Video demo

Enlace al video demostrativo del sistema (capturas de pantalla y recorrido por los modulos principales):

- _(pendiente de subir a YouTube/Google Drive por el estudiante)_

Una vez publicado, reemplazar esta linea por la URL definitiva.

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
- Historial academico con titulos e instituciones, adjuntando archivos PDF.
- Edad calculada automaticamente a partir de la fecha de nacimiento.

### Reportes y estadisticas

- Reporte visual con indicador de integridad OpenSSL (verde/rojo).
- Busqueda por documento, nombre, correo, direccion u ocupacion.
- Filtros por sexo y rango de edad.
- Paginacion en el reporte.
- Estadisticas por sexo y rangos de edad.
- Exportacion a Excel `.xlsx` respetando los filtros activos.

### Vacaciones

- Solicitudes de vacaciones con fechas de salida y retorno.
- Calculo automatico de dias generados (1 mes por cada 11 meses trabajados).
- Aprobacion o rechazo de solicitudes.

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

| Clase                  | Responsabilidad                                                                          |
| ---------------------- | ---------------------------------------------------------------------------------------- |
| `Database`           | Conexion PDO singleton                                                                   |
| `Catalogo`           | Catalogos de ocupaciones, tipos de empleado, planillas, motivos, departamentos y estados |
| `Colaborador`        | CRUD, promociones, bajas, historial, estadisticas y reportes                             |
| `Vacacion`           | Solicitudes y calculo de dias generados                                                  |
| `HistorialAcademico` | Registros academicos en PDF                                                              |
| `Usuario`            | Usuarios, roles y bitacora                                                               |
| `Rol`                | Roles y permisos                                                                         |
| `AuthService`        | Sesion, login, permisos y bloqueos                                                       |
| `IntegrityService`   | Firma y verificacion OpenSSL                                                             |
| `ExcelExportService` | Genera archivos Excel                                                                    |
| `FileUploadService`  | Subida de fotografias y PDFs                                                             |
| `Validator`          | Validaciones de entrada                                                                  |
| `Sanitizer`          | Sanitizacion de datos                                                                    |

## API REST

Endpoint publico (requiere permiso `api.consultar`):

```text
GET publico/index.php?route=api.colaboradores.sexo
```

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

## Consideraciones de seguridad

- Validaciones realizadas en el backend.
- Contrasenas almacenadas con `password_hash` (bcrypt).
- Longitud de contrasenas restringida entre 8 y 12 caracteres.
- Separacion de responsabilidades siguiendo MVC.
- Reutilizacion de conexion, validaciones y respuestas JSON (DRY).

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

## Autor

- Andre Reboulet
- Cedula: 8-1026-1624
- Grupo: 1GS131
- Profesora: Irina Fong
