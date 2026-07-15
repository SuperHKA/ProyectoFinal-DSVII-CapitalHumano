USE CapitalHumano;

-- Permiso independiente para abrir un nuevo periodo laboral sin borrar la baja previa.
INSERT INTO permisos (modulo, accion, descripcion)
SELECT 'colaboradores', 'reincorporar', 'Reincorporar colaboradores dados de baja'
WHERE NOT EXISTS (
    SELECT 1
    FROM permisos
    WHERE modulo = 'colaboradores' AND accion = 'reincorporar'
);

-- Administradores y operadores pueden ejecutar la reincorporacion.
INSERT IGNORE INTO rol_permiso (rol_id, permiso_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permisos p
    ON p.modulo = 'colaboradores' AND p.accion = 'reincorporar'
WHERE r.nombre IN ('Administrador', 'Operador');
