<?php
require_once __DIR__ . '/../vendor/autoload.php';

class ReportService {
    private $mpdf;

    public function __construct() {
        // mPDF needs at least 128 MB; shared hosting often defaults to 64 MB
        $current = ini_get('memory_limit');
        if ((int)$current < 256) {
            @ini_set('memory_limit', '256M');
        }
        // Extend execution time for large reports
        @set_time_limit(120);

        $this->initMpdf();
    }

    private function initMpdf() {
        // Use system temp dir — always writable on any host
        $tempDir = rtrim(sys_get_temp_dir(), '/\\') . '/mpdf_aulapro';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $this->mpdf = new \Mpdf\Mpdf([
            'tempDir'        => $tempDir,
            'margin_left'    => 10,
            'margin_right'   => 10,
            'margin_top'     => 40,
            'margin_bottom'  => 10,
            'margin_header'  => 5,
            'format'         => 'A4',
            'mode'           => 'utf-8',
            'default_font'   => 'dejavusans',
        ]);

        $this->mpdf->SetTitle('AulaPro Report');
        $this->mpdf->SetAuthor('AulaPro System');
    }

    public function generateBoletines($cfg, $ciclo, $estudiantes, $baseUrl = '') {
        $first = true;
        foreach ($estudiantes as $estudiante) {
            if (!$first) $this->mpdf->AddPage();
            $first = false;

            ob_start();
            $notas = $estudiante['modulos'] ?? [];
            include __DIR__ . '/../templates/pdf/boletin_modern.php';
            $html = ob_get_clean();
            $this->mpdf->WriteHTML($html);
        }
        return $this->mpdf;
    }

    public function generateListado($cfg, $ciclo, $estudiantes) {
        $this->mpdf->AddPage('L');
        ob_start();
        include __DIR__ . '/../templates/pdf/listado.php';
        $html = ob_get_clean();
        $this->mpdf->WriteHTML($html);
        return $this->mpdf;
    }

    public function generateHorario($cfg, $ciclo, $celdas, $franjas, $dias) {
        $this->mpdf->AddPage('L');
        ob_start();
        include __DIR__ . '/../templates/pdf/horario.php';
        $html = ob_get_clean();
        $this->mpdf->WriteHTML($html);
        return $this->mpdf;
    }

    public function stream($filename) {
        // Clear any accidental output before sending the PDF binary
        if (ob_get_level()) ob_end_clean();
        $this->mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
    }
}
