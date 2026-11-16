<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Pembuat PDF asesmen menggunakan Dompdf.
 *
 * Template HTML berada di app/Views/asesmen/pdf.php sehingga tampilan PDF
 * mudah diubah tanpa menyentuh logika controller.
 */
class AsesmenPdf
{
    /**
     * Render asesmen menjadi berkas PDF.
     *
     * @param array<string, mixed> $data
     */
    public function render(array $data): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('asesmen/pdf', ['asesmen' => $data]), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            430,
            812,
            'Halaman {PAGE_NUM} dari {PAGE_COUNT}',
            'DejaVu Sans',
            7.5,
            [0.47, 0.43, 0.6]
        );

        return $dompdf->output();
    }

    /**
     * Nama berkas unduhan yang aman.
     *
     * @param array<string, mixed> $data
     */
    public function filename(array $data): string
    {
        $parts = [
            'Asesmen',
            (string) ($data['subject'] ?? ''),
            (string) ($data['class'] ?? ''),
            (string) ($data['title'] ?? ''),
        ];

        $name = implode('_', array_filter(array_map('trim', $parts)));
        $name = preg_replace('/[^A-Za-z0-9]+/', '_', $name) ?? 'Asesmen';
        $name = trim(preg_replace('/_+/', '_', $name) ?? 'Asesmen', '_');
        $name = substr($name !== '' ? $name : 'Asesmen', 0, 90);

        return $name . '.pdf';
    }
}
