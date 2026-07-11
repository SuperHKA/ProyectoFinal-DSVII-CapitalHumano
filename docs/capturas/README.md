# Capturas de pantalla

Este directorio debe contener las imagenes PNG referenciadas por `docs/manual_usuario.md`.

## Lista de capturas requeridas

| Archivo | Pantalla |
| --- | --- |
| `01_login.png` | Inicio de sesion |
| `02_dashboard.png` | Panel general (Inicio) |
| `03_colaboradores_listado.png` | Listado de colaboradores |
| `04_colaboradores_nuevo.png` | Formulario de nuevo colaborador |
| `05_colaboradores_detalle.png` | Detalle del colaborador (perfil, historial, vacaciones) |
| `06_vacaciones.png` | Solicitudes de vacaciones |
| `07_reportes.png` | Reportes y graficos |
| `08_usuarios_roles.png` | Administracion de usuarios y roles |
| `09_bitacora.png` | Bitacora de accesos |

## Como generarlas

Desde PowerShell, con el WampServer corriendo y la sesion iniciada en Chrome:

```powershell
# Requiere Chrome instalado. Ajusta la URL base si no usas la ruta por defecto.
$base = "http://localhost/Software7/Proyectos/Software7-ProyectoFinal_Version_2/publico"
$rutas = @{
    "01_login.png"            = "$base/index.php?route=screen.login"
    "02_dashboard.png"        = "$base/index.php?route=screen.dashboard"
    "03_colaboradores_listado.png" = "$base/index.php?route=screen.colaboradores.list"
    "04_colaboradores_nuevo.png"   = "$base/index.php?route=screen.colaboradores.create"
    "05_colaboradores_detalle.png" = "$base/index.php?route=screen.colaboradores.show&id=1"
    "06_vacaciones.png"       = "$base/index.php?route=screen.vacaciones.list"
    "07_reportes.png"         = "$base/index.php?route=screen.reportes"
    "08_usuarios_roles.png"   = "$base/index.php?route=screen.usuarios.list"
    "09_bitacora.png"         = "$base/index.php?route=bitacora"
}
$out = "docs/capturas"
New-Item -ItemType Directory -Force -Path $out | Out-Null
foreach ($k in $rutas.Keys) {
    Start-Process "chrome.exe" "--headless --disable-gpu --window-size=1440,900 --screenshot=`"$PWD/$out/$k`" `"$($rutas[$k])`""
    Start-Sleep -Seconds 2
}
```

> Nota: Chrome headless no conserva la sesion entre capturas. Para mantener el login, primero exporta la cookie de sesion con `chrome --user-data-dir` y reutiliza el perfil, o usa una extension como **Fireshot** para capturar manualmente.
