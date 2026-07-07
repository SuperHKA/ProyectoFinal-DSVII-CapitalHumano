# Manual de usuario — Sistema de Capital Humano

## 1. Introducción

El Sistema de Capital Humano permite gestionar colaboradores, perfiles laborales, usuarios, roles, vacaciones y reportes, cumpliendo con requisitos de seguridad y auditoría.

## 2. Requisitos previos

- PHP 8.1 o superior.
- MySQL/MariaDB.
- Servidor web (Apache/Nginx).
- Composer.

## 3. Instalación

1. Clonar o copiar el proyecto al directorio del servidor web.
2. Ejecutar `composer install`.
3. Copiar `.env.example` a `.env` y configurar la base de datos.
4. Importar `database/capital_humano.sql`.
5. Generar llaves OpenSSL en `config/keys/` si no existen.
6. Abrir la URL pública.

## 4. Inicio de sesión

**Credenciales de prueba:**

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| admin | Admin123! | Administrador |

![Captura: pantalla de login](capturas/01_login.png)

## 5. Menú principal

El menú horizontal muestra las opciones disponibles según los permisos del rol. Desde cualquier pantalla se puede presionar **HOME** para regresar al inicio.

![Captura: menú principal](capturas/02_menu.png)

## 6. Gestión de colaboradores

### 6.1 Registrar colaborador

1. Ir a **Nuevo colaborador**.
2. Completar datos personales, contacto y perfil laboral.
3. Subir fotografía (opcional).
4. Guardar.

![Captura: formulario de colaborador](capturas/03_colaborador_create.png)

### 6.2 Ver detalle

En el detalle se muestra la fotografía, datos personales, historial académico y el historial laboral con el indicador de integridad OpenSSL.

![Captura: detalle del colaborador](capturas/04_colaborador_show.png)

### 6.3 Editar

Al editar se cargan los datos actuales, incluyendo el perfil laboral activo.

![Captura: edición de colaborador](capturas/05_colaborador_edit.png)

### 6.4 Promociones y bajas

- **Promoción:** crea un nuevo cargo y desactiva el anterior.
- **Baja:** registra fecha fin, motivo y desactiva al colaborador.

## 7. Reportes

1. Ir a **Reporte**.
2. Usar los filtros de texto, sexo y rango de edad.
3. Presionar **Buscar**.
4. Usar los números de página para navegar.
5. Presionar **Exportar Excel** para descargar el informe.

![Captura: reportes](capturas/06_reportes.png)

## 8. Vacaciones

1. Ir a **Vacaciones**.
2. Crear nueva solicitud seleccionando colaborador y fechas.
3. El sistema calcula automáticamente los días generados.
4. Un usuario autorizado puede aprobar o rechazar.

![Captura: vacaciones](capturas/07_vacaciones.png)

## 9. Usuarios y roles

Solo el administrador puede crear usuarios, asignarles múltiples roles y definir permisos por módulo.

![Captura: usuarios](capturas/08_usuarios.png)

## 10. Bitácora

Registra cada intento de acceso, indicando éxito, fracaso o anomalía después de 3 intentos fallidos.

![Captura: bitácora](capturas/09_bitacora.png)

## 11. Consideraciones de seguridad

- Los formularios incluyen token CSRF.
- Las contraseñas se almacenan con `password_hash`.
- Las consultas usan PDO preparado.
- Los datos sensibles de perfiles laborales se firman con OpenSSL.
