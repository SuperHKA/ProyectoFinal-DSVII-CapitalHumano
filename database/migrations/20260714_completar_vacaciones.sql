USE CapitalHumano;

-- La API usa una credencial exclusiva de Contraloria, no permisos de sesion web.
DELETE FROM permisos WHERE modulo = 'api' AND accion = 'consultar';

ALTER TABLE solicitudes_vacaciones
    ADD COLUMN saldo_antes DECIMAL(8,2) NOT NULL DEFAULT 0 AFTER dias_generados,
    ADD COLUMN numero_resuelto VARCHAR(30) NULL AFTER observaciones,
    ADD COLUMN fecha_resuelto DATETIME NULL AFTER numero_resuelto,
    ADD COLUMN aprobado_por INT NULL AFTER fecha_resuelto,
    ADD UNIQUE KEY uq_vacaciones_resuelto (numero_resuelto),
    ADD KEY idx_vacaciones_aprobador (aprobado_por),
    ADD CONSTRAINT fk_vacaciones_aprobador
        FOREIGN KEY (aprobado_por) REFERENCES usuarios(id)
        ON DELETE SET NULL ON UPDATE CASCADE;
