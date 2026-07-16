<?php

declare(strict_types=1);

namespace App\Servicios;

use DateTimeImmutable;

/**
 * Genera un resuelto PDF autocontenido sin depender de un motor externo.
 */
final class PdfResolutionService
{
    private const PAGE_WIDTH = 612;
    private const PAGE_HEIGHT = 792;

    /**
     * @param array<string,mixed> $resolution
     */
    public function render(array $resolution): string
    {
        $number = $this->value($resolution['numero_resuelto'] ?? null, 'Sin número');
        $employee = $this->value($resolution['colaborador'] ?? null, 'Colaborador no identificado');
        $identity = $this->value($resolution['identidad'] ?? null, 'No registrada');
        $department = $this->value($resolution['departamento'] ?? null, 'No asignado');
        $observations = $this->value($resolution['observaciones'] ?? null, 'Sin observaciones.');
        $approvedBy = $this->value($resolution['aprobado_por_nombre'] ?? null, 'Usuario autorizado');

        $content = "q\n";
        $content .= $this->fillRect(0, 700, self::PAGE_WIDTH, 92, '0.055 0.118 0.235');
        $content .= $this->fillRect(0, 700, 8, 92, '0.145 0.388 0.922');
        $content .= $this->text('CAPITAL HUMANO  /  GESTIÓN DE PERSONAS', 42, 759, 9, 'F2', '0.741 0.820 0.941');
        $content .= $this->text('RESUELTO DE VACACIONES', 42, 725, 21, 'F2', '1 1 1');

        $content .= $this->fillRect(42, 641, 528, 39, '0.941 0.961 1');
        $content .= $this->fillRect(42, 641, 4, 39, '0.145 0.388 0.922');
        $content .= $this->text('NÚMERO', 56, 662, 7.5, 'F2', '0.392 0.455 0.561');
        $content .= $this->text($number, 56, 648, 10.5, 'F2', '0.059 0.090 0.165');
        $content .= $this->text('FECHA DE EMISIÓN', 334, 662, 7.5, 'F2', '0.392 0.455 0.561');
        $content .= $this->text($this->formatDateTime($resolution['fecha_resuelto'] ?? null), 334, 648, 10, 'F1', '0.059 0.090 0.165');

        $content .= $this->text(
            'Se aprueba el período de vacaciones solicitado para el siguiente colaborador:',
            42,
            615,
            10,
            'F1',
            '0.278 0.333 0.412'
        );

        $content .= $this->fillRect(42, 520, 528, 76, '0.976 0.980 0.988');
        $content .= $this->fillRect(42, 520, 5, 76, '0.071 0.659 0.518');
        $content .= $this->text('COLABORADOR', 57, 578, 7.5, 'F2', '0.392 0.455 0.561');
        $content .= $this->text($employee, 57, 555, 15, 'F2', '0.059 0.090 0.165');
        $content .= $this->text('Identificación', 57, 535, 7.5, 'F2', '0.392 0.455 0.561');
        $content .= $this->text($identity, 125, 535, 9.5, 'F1', '0.173 0.220 0.294');
        $content .= $this->text('Departamento', 320, 535, 7.5, 'F2', '0.392 0.455 0.561');
        $content .= $this->text($department, 397, 535, 9.5, 'F1', '0.173 0.220 0.294');

        $content .= $this->text('PERÍODO AUTORIZADO', 42, 491, 8, 'F2', '0.392 0.455 0.561');
        $content .= $this->strokeRect(42, 443, 528, 35, '0.796 0.835 0.902', 0.8);
        $content .= $this->text(
            $this->formatDate($resolution['fecha_inicio'] ?? null) . '  —  ' . $this->formatDate($resolution['fecha_fin'] ?? null),
            56,
            456,
            11.5,
            'F2',
            '0.059 0.090 0.165'
        );
        $content .= $this->fillRect(459, 443, 111, 35, '0.145 0.388 0.922');
        $content .= $this->text('DÍAS AUTORIZADOS', 473, 464, 6.5, 'F2', '0.859 0.906 1');
        $content .= $this->text((string) ($resolution['dias_solicitados'] ?? 0), 473, 450, 13, 'F2', '1 1 1');

        $content .= $this->metricCard(
            42,
            'DÍAS GENERADOS',
            $this->formatAmount($resolution['dias_generados'] ?? 0),
            'A la fecha de solicitud'
        );
        $content .= $this->metricCard(
            220,
            'SALDO ANTERIOR',
            $this->formatAmount($resolution['saldo_antes'] ?? 0),
            'Días disponibles'
        );
        $content .= $this->metricCard(
            398,
            'SALDO POSTERIOR',
            $this->formatAmount($resolution['saldo_despues'] ?? 0),
            'Días estimados'
        );

        $content .= $this->text('OBSERVACIONES', 42, 345, 8, 'F2', '0.392 0.455 0.561');
        $content .= $this->fillRect(42, 283, 528, 47, '0.976 0.980 0.988');
        $content .= $this->wrappedText($observations, 56, 311, 496, 10, 14, 2, 'F1', '0.173 0.220 0.294');

        $content .= $this->text('APROBACIÓN', 42, 248, 8, 'F2', '0.392 0.455 0.561');
        $content .= $this->line(42, 205, 264, 205, '0.588 0.639 0.710', 0.7);
        $content .= $this->text($approvedBy, 42, 188, 10.5, 'F2', '0.059 0.090 0.165');
        $content .= $this->text('Usuario autorizado', 42, 174, 8, 'F1', '0.392 0.455 0.561');

        $content .= $this->fillRect(394, 194, 176, 36, '0.925 0.988 0.965');
        $content .= $this->fillRect(394, 194, 4, 36, '0.071 0.659 0.518');
        $content .= $this->text('ESTADO', 409, 216, 6.5, 'F2', '0.047 0.471 0.349');
        $content .= $this->text('APROBADA', 409, 202, 10.5, 'F2', '0.047 0.471 0.349');

        $content .= $this->fillRect(0, 0, self::PAGE_WIDTH, 62, '0.055 0.118 0.235');
        $content .= $this->text(
            'Documento generado electrónicamente por el Sistema de Capital Humano.',
            42,
            34,
            8.5,
            'F1',
            '0.741 0.820 0.941'
        );
        $content .= $this->text('PÁGINA 1 DE 1', 486, 34, 7.5, 'F2', '0.741 0.820 0.941');
        $content .= "Q\n";

        return $this->buildPdf($content, $number);
    }

    private function metricCard(float $x, string $label, string $amount, string $caption): string
    {
        $content = $this->fillRect($x, 366, 162, 58, '0.976 0.980 0.988');
        $content .= $this->text($label, $x + 14, 406, 6.8, 'F2', '0.392 0.455 0.561');
        $content .= $this->text($amount, $x + 14, 385, 14, 'F2', '0.059 0.090 0.165');
        $content .= $this->text($caption, $x + 14, 373, 6.8, 'F1', '0.392 0.455 0.561');

        return $content;
    }

    private function text(
        mixed $value,
        float $x,
        float $y,
        float $size,
        string $font,
        string $color
    ): string {
        return sprintf(
            "BT /%s %.2F Tf %s rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
            $font,
            $size,
            $color,
            $x,
            $y,
            $this->escape($value)
        );
    }

    private function wrappedText(
        string $value,
        float $x,
        float $y,
        float $width,
        float $size,
        float $lineHeight,
        int $maxLines,
        string $font,
        string $color
    ): string {
        $maxCharacters = max(10, (int) floor($width / ($size * 0.52)));
        $lines = explode("\n", wordwrap($value, $maxCharacters, "\n", true));
        $lines = array_slice($lines, 0, $maxLines);
        $content = '';
        foreach ($lines as $index => $line) {
            $content .= $this->text($line, $x, $y - ($index * $lineHeight), $size, $font, $color);
        }

        return $content;
    }

    private function fillRect(float $x, float $y, float $width, float $height, string $color): string
    {
        return sprintf("%s rg %.2F %.2F %.2F %.2F re f\n", $color, $x, $y, $width, $height);
    }

    private function strokeRect(
        float $x,
        float $y,
        float $width,
        float $height,
        string $color,
        float $lineWidth
    ): string {
        return sprintf(
            "%s RG %.2F w %.2F %.2F %.2F %.2F re S\n",
            $color,
            $lineWidth,
            $x,
            $y,
            $width,
            $height
        );
    }

    private function line(
        float $x1,
        float $y1,
        float $x2,
        float $y2,
        string $color,
        float $lineWidth
    ): string {
        return sprintf(
            "%s RG %.2F w %.2F %.2F m %.2F %.2F l S\n",
            $color,
            $lineWidth,
            $x1,
            $y1,
            $x2,
            $y2
        );
    }

    private function buildPdf(string $content, string $number): string
    {
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . self::PAGE_WIDTH . ' ' . self::PAGE_HEIGHT . '] '
                . '/Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents 4 0 R >>',
            '<< /Length ' . strlen($content) . ">>\nstream\n" . $content . "endstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
            '<< /Title (' . $this->escape('Resuelto de vacaciones ' . $number) . ') '
                . '/Producer (Sistema de Capital Humano) /CreationDate (D:' . date('YmdHis') . ') >>',
        ];

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $number => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($number + 1) . " 0 obj\n{$object}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($index = 1; $index <= count($objects); $index++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$index]) . "\n";
        }
        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . " /Root 1 0 R /Info 7 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    private function formatDate(mixed $value): string
    {
        if (!is_string($value) || trim($value) === '') {
            return 'Fecha no registrada';
        }

        try {
            $date = new DateTimeImmutable($value);
        } catch (\Throwable) {
            return $value;
        }

        $months = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        return (int) $date->format('j') . ' de ' . $months[(int) $date->format('n')] . ' de ' . $date->format('Y');
    }

    private function formatDateTime(mixed $value): string
    {
        $source = is_string($value) && trim($value) !== '' ? $value : 'now';
        try {
            $date = new DateTimeImmutable($source);
        } catch (\Throwable) {
            return (string) $value;
        }

        return $this->formatDate($date->format('Y-m-d')) . ', ' . $date->format('H:i');
    }

    private function formatAmount(mixed $value): string
    {
        return number_format((float) $value, 2, '.', ',');
    }

    private function value(mixed $value, string $fallback): string
    {
        $text = trim((string) ($value ?? ''));

        return $text !== '' ? $text : $fallback;
    }

    private function escape(mixed $value): string
    {
        $encoded = mb_convert_encoding((string) $value, 'Windows-1252', 'UTF-8');

        return str_replace(
            ['\\', '(', ')', "\r", "\n"],
            ['\\\\', '\\(', '\\)', '', ' '],
            $encoded
        );
    }
}
