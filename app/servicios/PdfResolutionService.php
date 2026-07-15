<?php

declare(strict_types=1);

namespace App\Servicios;

/**
 * Genera un PDF textual autocontenido sin dependencias externas.
 */
final class PdfResolutionService
{
    /**
     * @param array<string,mixed> $resolution
     */
    public function render(array $resolution): string
    {
        $lines = [
            'SISTEMA DE CAPITAL HUMANO',
            'RESUELTO DE VACACIONES',
            '',
            'Numero: ' . ($resolution['numero_resuelto'] ?? ''),
            'Fecha de emision: ' . ($resolution['fecha_resuelto'] ?? date('Y-m-d H:i:s')),
            '',
            'Se resuelve aprobar el periodo de vacaciones de:',
            (string) ($resolution['colaborador'] ?? ''),
            'Identificacion: ' . ($resolution['identidad'] ?? ''),
            'Departamento: ' . ($resolution['departamento'] ?? 'No asignado'),
            '',
            'Periodo autorizado: ' . ($resolution['fecha_inicio'] ?? '') . ' al ' . ($resolution['fecha_fin'] ?? ''),
            'Dias autorizados: ' . ($resolution['dias_solicitados'] ?? 0),
            'Dias generados a la solicitud: ' . ($resolution['dias_generados'] ?? 0),
            'Saldo anterior: ' . ($resolution['saldo_antes'] ?? 0),
            'Saldo posterior estimado: ' . ($resolution['saldo_despues'] ?? 0),
            '',
            'Observaciones: ' . ($resolution['observaciones'] ?? 'Sin observaciones'),
            '',
            'Aprobado por: ' . ($resolution['aprobado_por_nombre'] ?? 'Usuario autorizado'),
            '',
            'Documento generado electronicamente por el Sistema de Capital Humano.',
        ];

        $content = "BT\n/F1 11 Tf\n50 790 Td\n14 TL\n";
        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $content .= "T*\n";
            }
            $content .= '(' . $this->escape($line) . ") Tj\n";
        }
        $content .= "ET";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>',
            '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $number => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($number + 1) . " 0 obj\n{$object}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }
        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    private function escape(mixed $value): string
    {
        $latin = mb_convert_encoding((string) $value, 'ISO-8859-1', 'UTF-8');
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ' '], $latin);
    }
}
