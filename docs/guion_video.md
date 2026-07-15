# Guion del video demostrativo

**Proyecto:** Sistema de Capital Humano  
**Duración estimada:** 7 minutos y 30 segundos  
**Formato recomendado:** narración sobre una grabación de pantalla, sin leer código completo.

## Preparación antes de grabar

- Abrir el sistema en la página pública y tener preparada otra pestaña con el inicio de sesión.
- Usar el usuario `admin` para que todos los módulos estén disponibles.
- Tener localizados a **Sofía Hernández**, que posee una promoción, y **Roberto Díaz**, retirado por jubilación.
- Preparar el reporte con algunos filtros y una solicitud de vacaciones de ejemplo.
- Tener una terminal preparada para mostrar la API sin revelar el valor de `CONTRALORIA_API_TOKEN`.
- No mostrar el contenido de `.env`, contraseñas, llaves privadas ni tokens durante la grabación.
- Grabar a una resolución mínima de 1280 × 720 y usar un zoom del navegador entre 100 % y 110 %.

## 0:00–0:25 — Presentación

**Mostrar en pantalla:** diapositiva con el nombre del proyecto, versión 2.0, integrantes y asignatura.

**Narración:**

> Buenos días. Somos Aaron Ortiz, Orlando Valdez, Gustavo Domínguez y Andre Reboulet. Presentamos la versión 2.0 de nuestro Sistema de Capital Humano, desarrollado en PHP con arquitectura MVC y una base de datos MariaDB. La solución permite gestionar de manera segura el ciclo laboral de los colaboradores, desde su contratación hasta promociones, vacaciones, reportes o bajas.

## 0:25–0:55 — Página pública

**Mostrar en pantalla:** recorrer lentamente la portada; señalar beneficios, arquitectura, seguridad y botón de acceso.

**Narración:**

> Al ingresar encontramos una página pública que presenta el propósito y las capacidades principales del sistema. Esta sección no expone información administrativa; únicamente explica los beneficios, la arquitectura tecnológica y los controles de seguridad. Su diseño comparte la identidad visual del panel y utiliza animaciones suaves al desplazarse. Desde aquí podemos acceder al portal protegido.

## 0:55–1:35 — Autenticación y acceso por roles

**Mostrar en pantalla:** abrir el inicio de sesión, ingresar como administrador y señalar el menú resultante.

**Narración:**

> El acceso administrativo requiere usuario y contraseña. Las contraseñas no se guardan en texto plano, sino mediante un servicio de hash que implementa un contrato criptográfico común. El sistema registra los intentos de acceso y bloquea temporalmente la cuenta después de tres intentos fallidos. Una vez autenticado, el menú se construye según los permisos del usuario. En este caso usamos el rol administrador, que puede gestionar colaboradores, reportes, vacaciones, usuarios, roles y bitácora.

## 1:35–2:55 — Gestión de colaboradores

**Mostrar en pantalla:** lista de colaboradores, búsqueda, ficha de Sofía Hernández y su historial de cargos. Después abrir la ficha de Roberto Díaz.

**Narración:**

> El módulo de colaboradores centraliza los datos personales y laborales. La lista permite buscar por documento, nombre, correo, dirección u ocupación. Cada registro contiene nombres separados, fecha de nacimiento, sexo, contacto, fotografía, estado, departamento, planilla, ocupación, salario y fecha de ingreso.
>
> En la ficha de Sofía Hernández podemos observar dos perfiles laborales. El primero fue cerrado automáticamente cuando se registró su promoción y el segundo representa su cargo actual. De esta forma no se reemplaza la trayectoria anterior, sino que se conserva como historial verificable.
>
> También se pueden registrar bajas indicando fecha y motivo. Por ejemplo, Roberto Díaz aparece inactivo por jubilación y conserva su perfil y sus fechas laborales. El sistema distingue a los colaboradores activos de quienes fueron retirados o dados de baja.

**Acción opcional:** abrir el formulario “Nuevo colaborador”, sin enviarlo, y recorrer sus secciones.

**Narración opcional:**

> Al crear o editar un colaborador, los datos son validados en el servidor. Las fotografías se verifican por su contenido real mediante Fileinfo, reciben un nombre generado por el sistema y se normalizan como miniaturas. Así no se confía en el nombre ni en el tipo de archivo enviado por el navegador.

## 2:55–3:55 — Reportes, estadísticas e integridad

**Mostrar en pantalla:** entrar a Reportes; aplicar filtros de sexo, edad, salario y dirección; mostrar estadísticas y el indicador verde de integridad. Pulsar “Exportar Excel” si se desea mostrar la descarga.

**Narración:**

> El módulo de reportes transforma los registros en información para la toma de decisiones. Podemos combinar búsquedas con filtros por sexo, rango de edad, salario mínimo, salario máximo y dirección. En la parte superior se presentan estadísticas agrupadas por sexo, edad y ubicación. La exportación a Excel respeta exactamente los filtros seleccionados.
>
> Cada perfil laboral incluye además un sello de integridad. El salario, cargo, departamento, planilla, código de empleado y fecha de inicio se firman con RSA y SHA-256. Al consultar el reporte, el sistema vuelve a verificar la firma. El indicador verde confirma que la información permanece íntegra; si alguien altera directamente un dato protegido en la base, la verificación falla y el registro se marca como vulnerado.

## 3:55–4:55 — Vacaciones y resuelto PDF

**Mostrar en pantalla:** módulo Vacaciones, tarjetas de resumen, formulario de solicitud, saldo disponible y botones de aprobación o rechazo. Abrir o generar un resuelto PDF de una solicitud aprobada.

**Narración:**

> En vacaciones, el sistema calcula los días generados desde la primera contratación, aplicando un día por cada once días trabajados. Antes de guardar una solicitud se comprueba que el colaborador esté activo, tenga saldo suficiente, solicite al menos siete días calendario y no existan fechas solapadas.
>
> Las solicitudes quedan pendientes hasta que un usuario autorizado las aprueba o rechaza. Al aprobarlas se registra el responsable y puede generarse un resuelto en PDF con un número único, las fechas, los días solicitados y la información del colaborador. Las tarjetas superiores permiten revisar rápidamente solicitudes pendientes, aprobadas y rechazadas.

## 4:55–5:45 — Usuarios, roles y bitácora

**Mostrar en pantalla:** lista de usuarios, edición de un rol con su matriz de permisos y bitácora de accesos.

**Narración:**

> La seguridad funcional se administra mediante usuarios, roles y permisos. Un rol puede recibir acciones específicas como ver, crear, editar, promover, dar de baja, aprobar vacaciones o exportar reportes. Esto permite que un operador tenga funciones de gestión mientras un consultor conserve acceso únicamente de lectura.
>
> La bitácora registra accesos exitosos y fallidos con usuario, dirección IP, fecha y detalle. Los usuarios se desactivan de forma lógica en lugar de eliminarse, manteniendo la trazabilidad de las operaciones.

## 5:45–6:35 — Arquitectura, protección y logs de errores

**Mostrar en pantalla:** vista breve del árbol de carpetas en el editor; señalar `controladores`, `modelos`, `servicios`, `vistas`, `contratos` y `almacenamiento/logs`. Abrir únicamente los nombres de las clases relevantes.

**Narración:**

> A nivel técnico, el proyecto utiliza una arquitectura MVC que separa controladores, modelos, servicios y vistas. Las consultas se ejecutan con PDO y parámetros preparados. Los formularios utilizan tokens CSRF, mientras las entradas son sanitizadas y validadas antes de llegar a la base de datos.
>
> La interfaz `CryptographicServiceInterface` define las operaciones de protección, verificación y algoritmo. Tanto el servicio de contraseñas como el servicio de firmas RSA implementan este contrato, aplicando principios SOLID y evitando que la lógica dependa directamente de una tecnología concreta.
>
> Los errores se guardan diariamente en la carpeta `almacenamiento/logs`. Cada incidente recibe un identificador y el logger elimina contraseñas, tokens y otros secretos del contexto. El usuario recibe un mensaje seguro, mientras el detalle técnico queda disponible para diagnóstico.

## 6:35–7:10 — API para Contraloría

**Mostrar en pantalla:** realizar primero una petición sin credencial para mostrar el `401`; luego una petición autorizada con el token oculto y mostrar la respuesta JSON.

**Narración:**

> El sistema también incorpora una API REST para la Contraloría General. El endpoint entrega la cantidad total de colaboradores y la distribución por sexo en formato JSON. Esta integración no acepta la sesión del administrador: requiere una credencial Bearer exclusiva y compara el token de forma segura. Sin credencial responde con código 401 y con una credencial válida devuelve únicamente la información autorizada.

**Comandos sugeridos, sin revelar el token:**

```bash
curl "http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php?route=api.colaboradores.sexo"

curl -H "Authorization: Bearer $CONTRALORIA_API_TOKEN" \
  "http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico/index.php?route=api.colaboradores.sexo"
```

## 7:10–7:30 — Cierre

**Mostrar en pantalla:** regresar a la portada o presentar una diapositiva con los módulos principales.

**Narración:**

> En conclusión, el Sistema de Capital Humano integra la trayectoria laboral, vacaciones, reportes, usuarios y auditoría en una sola plataforma. Además de cumplir las funciones administrativas, protege las cargas de archivos, restringe el acceso por roles, verifica la integridad de la información y ofrece una API segura. Con esto finalizamos la demostración. Muchas gracias.

## Distribución opcional entre los integrantes

| Integrante | Segmento sugerido |
| --- | --- |
| Aaron Ortiz | Presentación, página pública y objetivo del sistema |
| Orlando Valdez | Colaboradores, promociones, bajas y vacaciones |
| Gustavo Domínguez | Interfaz, reportes, estadísticas y experiencia de usuario |
| Andre Reboulet | Arquitectura, seguridad, contrato criptográfico, logs y API |

## Versión corta de emergencia

Si el video debe acercarse a 4 minutos, conservar estos segmentos: presentación, colaboradores, reportes e integridad, vacaciones, seguridad/API y cierre. Omitir el recorrido del formulario, reducir usuarios y roles a una sola frase y mostrar la arquitectura únicamente como una diapositiva.
