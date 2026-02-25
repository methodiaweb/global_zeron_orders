<?php
declare(strict_types=1);

namespace GZO\Front;

final class DummyPdf
{
    public function init(): void
    {
        add_action('init', [$this, 'maybe_output_pdf']);
    }

    public function maybe_output_pdf(): void
    {
        if (!isset($_GET['gzo_dummy_pdf']) || (string)$_GET['gzo_dummy_pdf'] !== '1') {
            return;
        }

        // Output a tiny PDF (placeholder) so FE can test "open in new tab".
        $type = isset($_GET['type']) ? sanitize_text_field((string)$_GET['type']) : 'doc';
        $doc = isset($_GET['doc']) ? sanitize_text_field((string)$_GET['doc']) : 'UNKNOWN';

        nocache_headers();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $type . '-' . $doc . '.pdf"');

        // Minimal PDF bytes.
        $text = "Global Zeron orders - Dummy PDF\nType: {$type}\nDocument: {$doc}\n";
        $pdf = $this->simple_pdf($text);
        echo $pdf;
        exit;
    }

    private function simple_pdf(string $text): string
    {
        // Very small single-page PDF generator (ASCII only).
        $safe = preg_replace('/[^\x20-\x7E\n]/', '', $text);
        $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $safe);
        $lines = explode("\n", trim($safe));

        $content = "BT\n/F1 12 Tf\n72 720 Td\n";
        foreach ($lines as $i => $line) {
            if ($i > 0) {
                $content .= "0 -16 Td\n";
            }
            $content .= "(" . $line . ") Tj\n";
        }
        $content .= "ET\n";

        $objects = [];
        $objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R>>endobj\n";
        $objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1>>endobj\n";
        $objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources<< /Font<< /F1 4 0 R >> >> /Contents 5 0 R >>endobj\n";
        $objects[] = "4 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";
        $objects[] = "5 0 obj<< /Length " . strlen($content) . " >>stream\n" . $content . "endstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xref . "\n%%EOF";
        return $pdf;
    }
}
