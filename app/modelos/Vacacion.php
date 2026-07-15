<?php

declare(strict_types=1);

namespace App\Modelos;

use Config\Database;
use DateTimeImmutable;
use PDO;
use RuntimeException;

/**
 * Gestiona devengo, saldo, solicitudes y resueltos de vacaciones.
 */
final class Vacacion
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * @return array<string,mixed>
     */
    public function paginated(int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $statement = $this->db->prepare(
            'SELECT v.*, CONCAT(c.primer_nombre, " ", c.primer_apellido) AS colaborador,
                    u.nombre AS aprobado_por_nombre
             FROM solicitudes_vacaciones v
             INNER JOIN colaboradores c ON c.id = v.colaborador_id
             LEFT JOIN usuarios u ON u.id = v.aprobado_por
             ORDER BY v.fecha_registro DESC, v.id DESC
             LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $total = (int) $this->db->query('SELECT COUNT(*) FROM solicitudes_vacaciones')->fetchColumn();

        return [
            'items' => $statement->fetchAll(),
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * @return array<string,int>
     */
    public function summary(): array
    {
        $result = ['Pendiente' => 0, 'Aprobada' => 0, 'Rechazada' => 0];
        foreach ($this->db->query('SELECT estado, COUNT(*) AS total FROM solicitudes_vacaciones GROUP BY estado')->fetchAll() as $row) {
            $result[(string) $row['estado']] = (int) $row['total'];
        }
        return $result;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function colaboradoresDisponibles(): array
    {
        $rows = $this->db->query(
            'SELECT id, CONCAT(primer_nombre, " ", primer_apellido) AS nombre_completo
             FROM colaboradores
             WHERE empleado_activo = 1
             ORDER BY primer_nombre, primer_apellido'
        )->fetchAll();
        foreach ($rows as &$row) {
            $row['saldo'] = $this->saldo((int) $row['id']);
        }
        return $rows;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): int
    {
        $ownsTransaction = !$this->db->inTransaction();
        if ($ownsTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $colaboradorId = (int) $data['colaborador_id'];
            $activeStatement = $this->db->prepare('SELECT empleado_activo FROM colaboradores WHERE id = :id FOR UPDATE');
            $activeStatement->execute(['id' => $colaboradorId]);
            if ((int) $activeStatement->fetchColumn() !== 1) {
                throw new RuntimeException('Solo se pueden solicitar vacaciones para colaboradores activos.');
            }
            $diasSolicitados = $this->diasEntre((string) $data['fecha_inicio'], (string) $data['fecha_fin']);
            if ($diasSolicitados < 7) {
                throw new RuntimeException('La solicitud debe cubrir al menos 7 dias calendario.');
            }
            if ($this->hasOverlap($colaboradorId, (string) $data['fecha_inicio'], (string) $data['fecha_fin'])) {
                throw new RuntimeException('El colaborador ya tiene una solicitud pendiente o aprobada en ese periodo.');
            }

            $balance = $this->saldo($colaboradorId);
            if ($diasSolicitados > (float) $balance['disponibles']) {
                throw new RuntimeException(
                    'Saldo insuficiente. Disponibles: ' . number_format((float) $balance['disponibles'], 2) . ' dias.'
                );
            }

            $statement = $this->db->prepare(
                'INSERT INTO solicitudes_vacaciones
                 (colaborador_id, fecha_inicio, fecha_fin, dias_solicitados, dias_generados, saldo_antes, observaciones)
                 VALUES
                 (:colaborador_id, :fecha_inicio, :fecha_fin, :dias_solicitados, :dias_generados, :saldo_antes, :observaciones)'
            );
            $statement->execute([
                'colaborador_id' => $colaboradorId,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'dias_solicitados' => $diasSolicitados,
                'dias_generados' => $balance['generados'],
                'saldo_antes' => $balance['disponibles'],
                'observaciones' => $data['observaciones'] !== '' ? $data['observaciones'] : null,
            ]);
            $requestId = (int) $this->db->lastInsertId();
            if ($ownsTransaction) {
                $this->db->commit();
            }
            return $requestId;
        } catch (\Throwable $exception) {
            if ($ownsTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    public function aprobar(int $id, int $usuarioId): void
    {
        $this->db->beginTransaction();
        try {
            $request = $this->lockedRequest($id);
            if ($request === null || $request['estado'] !== 'Pendiente') {
                throw new RuntimeException('La solicitud no existe o ya fue procesada.');
            }

            $number = sprintf('RH-VAC-%s-%06d', date('Y'), $id);
            $statement = $this->db->prepare(
                "UPDATE solicitudes_vacaciones
                 SET estado = 'Aprobada', numero_resuelto = :numero,
                     fecha_resuelto = NOW(), aprobado_por = :usuario
                 WHERE id = :id AND estado = 'Pendiente'"
            );
            $statement->execute(['numero' => $number, 'usuario' => $usuarioId, 'id' => $id]);
            if ($statement->rowCount() !== 1) {
                throw new RuntimeException('La solicitud fue procesada por otro usuario.');
            }
            $this->db->commit();
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    public function rechazar(int $id): void
    {
        $statement = $this->db->prepare(
            "UPDATE solicitudes_vacaciones SET estado = 'Rechazada' WHERE id = :id AND estado = 'Pendiente'"
        );
        $statement->execute(['id' => $id]);
        if ($statement->rowCount() !== 1) {
            throw new RuntimeException('La solicitud no existe o ya fue procesada.');
        }
    }

    /**
     * @return array<string,mixed>|null
     */
    public function resolution(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT v.*, CONCAT_WS(" ", c.primer_nombre, NULLIF(c.segundo_nombre, ""), c.primer_apellido, NULLIF(c.segundo_apellido, "")) AS colaborador,
                    c.identidad, d.nombre AS departamento, u.nombre AS aprobado_por_nombre,
                    GREATEST(v.saldo_antes - v.dias_solicitados, 0) AS saldo_despues
             FROM solicitudes_vacaciones v
             INNER JOIN colaboradores c ON c.id = v.colaborador_id
             LEFT JOIN perfiles_laborales p ON p.colaborador_id = c.id AND p.cargo_activo = 1
             LEFT JOIN departamentos d ON d.id = p.departamento_id
             LEFT JOIN usuarios u ON u.id = v.aprobado_por
             WHERE v.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    /**
     * @return array{generados:float,aprobados:float,pendientes:float,disponibles:float,fecha_contratacion:?string}
     */
    public function saldo(int $colaboradorId): array
    {
        $generated = $this->diasGenerados($colaboradorId);
        $statement = $this->db->prepare(
            "SELECT
                COALESCE(SUM(CASE WHEN estado = 'Aprobada' THEN dias_solicitados ELSE 0 END), 0) AS aprobados,
                COALESCE(SUM(CASE WHEN estado = 'Pendiente' THEN dias_solicitados ELSE 0 END), 0) AS pendientes
             FROM solicitudes_vacaciones WHERE colaborador_id = :id"
        );
        $statement->execute(['id' => $colaboradorId]);
        $used = $statement->fetch() ?: ['aprobados' => 0, 'pendientes' => 0];
        $approved = (float) $used['aprobados'];
        $pending = (float) $used['pendientes'];

        return [
            'generados' => $generated['dias'],
            'aprobados' => $approved,
            'pendientes' => $pending,
            'disponibles' => round(max(0, $generated['dias'] - $approved - $pending), 2),
            'fecha_contratacion' => $generated['fecha_contratacion'],
        ];
    }

    /**
     * @return array{dias:float,fecha_contratacion:?string}
     */
    public function diasGenerados(int $colaboradorId): array
    {
        $statement = $this->db->prepare(
            'SELECT MIN(fecha_inicio) FROM perfiles_laborales WHERE colaborador_id = :id'
        );
        $statement->execute(['id' => $colaboradorId]);
        $date = $statement->fetchColumn();
        if ($date === false || $date === null) {
            return ['dias' => 0.0, 'fecha_contratacion' => null];
        }

        $start = new DateTimeImmutable((string) $date);
        $today = new DateTimeImmutable('today');
        if ($start > $today) {
            return ['dias' => 0.0, 'fecha_contratacion' => (string) $date];
        }

        $workedDays = (int) $start->diff($today)->days;
        return [
            'dias' => round($workedDays / 11, 2),
            'fecha_contratacion' => (string) $date,
        ];
    }

    private function hasOverlap(int $colaboradorId, string $start, string $end): bool
    {
        $statement = $this->db->prepare(
            "SELECT COUNT(*) FROM solicitudes_vacaciones
             WHERE colaborador_id = :id AND estado IN ('Pendiente', 'Aprobada')
               AND fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_inicio"
        );
        $statement->execute(['id' => $colaboradorId, 'fecha_inicio' => $start, 'fecha_fin' => $end]);
        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * @return array<string,mixed>|null
     */
    private function lockedRequest(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM solicitudes_vacaciones WHERE id = :id FOR UPDATE');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    private function diasEntre(string $start, string $end): int
    {
        return (int) (new DateTimeImmutable($start))->diff(new DateTimeImmutable($end))->days + 1;
    }
}
