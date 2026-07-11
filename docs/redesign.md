# Redisenio Visual — Sistema de Capital Humano v2

Fecha: 2026-07-09
Alcance: Fase 2 del plan de redisenio (`federated-discovering-mango.md`).

## Que cambio

- **Sidebar fijo a la izquierda** con 2 secciones con submenu (Colaboradores, Admin). Sustituye al topbar plano anterior.
- **Modo claro + oscuro** con toggle en la cabecera. Persistido en `localStorage` (clave `ch-theme`).
- **Dos vistas nuevas** con KPIs y graficos:
  - `inicio` (alias de `home`) — 4 KPIs (colaboradores, activos, inactivos, % activos) + dona de sexo + barras de rangos de edad.
  - `admin.index` — 3 KPIs (usuarios totales, activos, roles) + tabla de ultimos accesos + tabla de roles.
- **Charts** (Chart.js 4.4.4 via CDN) en Inicio y Reportes. Colores leidos de CSS vars, reaccionan al tema.
- **Login** rediseado: split-panel con hero editorial + form (CSS ya estaba en `layouts.css`).

## Archivos nuevos

| Archivo | Proposito |
| --- | --- |
| `publico/assets/css/dark.css` | Tokens para `[data-theme="dark"]` |
| `publico/assets/js/theme.js` | Toggle + persistencia (tambien aplica al cargar) |
| `publico/assets/js/nav.js` | Submenus (dropdown) + menu mobile (drawer + backdrop) |
| `publico/assets/js/ui.js` | Password toggle, toasts, table search, modal, confirm |
| `publico/assets/js/charts.js` | Helpers `doughnutSexo` y `barEdad` (leen CSS vars) |
| `app/vistas/layouts/sidebar.php` | Sidebar con submenus |
| `app/vistas/inicio/index.php` | Panel general con KPIs + 2 charts |
| `app/vistas/admin/index.php` | Resumen administrativo |
| `app/controladores/InicioController.php` | Sirve `inicio` |
| `app/controladores/AdminController.php` | Sirve `admin.index` |

## Archivos modificados

- `publico/assets/css/tokens.css` — tokens semanticos (`--chart-1..6`, `--sidebar-w`, `--topbar-h`, `--content-max`).
- `publico/assets/css/base.css` — `color-scheme: light dark`, `prefers-reduced-motion`, skip-link, scrollbars discretos.
- `publico/assets/css/components.css` — `.badge--ok/warn/danger/info/accent`, `.alert--success/danger/warn/info`, `.card`, `.chart-card`, `.dropdown` (con `data-open` + ARIA), `.modal`, `.skeleton`, `.empty-state`, `.action-btn--*`, `.form-grid`, `.form-section`, `.form-actions`, `.filters`, `.theme-toggle`.
- `publico/assets/css/layouts.css` — sidebar como drawer en mobile (backdrop + transform), `.stats-grid`, `.stat-card`, `.report-thumb`, `.profile-title`, `.avatar`.
- `app/vistas/layouts/header.php` — script inline anti-FOUC para tema, 5 CSS modernas (incluye `dark.css`), 4 JS (theme, nav, ui, charts), Chart.js CDN, sidebar incluida, topbar con busqueda global + theme toggle + avatar + logout.
- `app/vistas/layouts/footer.php` — reestructurado para soportar shell con sidebar y modo standalone.
- `app/vistas/reportes/index.php` — 2 charts arriba + toolbar de filtros + card de resumen. Rangos de edad actualizados a 20-25 / 25-30 / 30-35 / mayor-35 (Contexto.md).
- `app/modelos/Colaborador.php` — `buildFilterSql` y `estadisticasPorEdad` usan los 4 buckets del Contexto.md.
- `rutas/web.php` — registradas `inicio` y `admin.index`.
- `app/controladores/ScreenController.php` — eliminado (rutas `screen.*` removidas en limpieza).

## Accesibilidad

- Skip link "Saltar al contenido principal" al inicio.
- Foco visible (anillo 2px) en todos los interactivos via `:focus-visible`.
- `prefers-reduced-motion: reduce` desactiva transiciones.
- Contraste AA validado en los tokens OKLCH.
- Submenus con `aria-haspopup`, `aria-expanded`, `role="group"`, `aria-label`.
- Charts con `role="img"` + `aria-label` descriptivo.
- Cierre de menu mobile + dropdowns con `Escape`.

## Como probar

1. Visitar `http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/` y entrar con `admin / Admin123!`.
2. Ver el panel "Inicio" con 4 KPIs y 2 charts.
3. Clickear el icono de luna/sol en la cabecera para alternar tema. Refrescar: el tema persiste.
4. Clickear "Admin" en la sidebar para ver el resumen administrativo.
5. Redimensionar la ventana a < 900px: la sidebar se convierte en drawer con boton hamburger.
6. Clickear "Reportes": debe mostrar los mismos 2 charts + tabla de filtros + tabla de datos.

## Pendiente

- **Borrar `publico/assets/css/styles.css`** — se difiere a 1 dia de uso sin regresiones, segun el plan de referencia. Aun no se elimina.
- **Capturas del manual de usuario** — `docs/capturas/` sigue vacio. Ver `docs/capturas/README.md` para la guia PowerShell de generacion.
