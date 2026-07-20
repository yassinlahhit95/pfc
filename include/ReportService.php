<?php
require_once __DIR__ . '/../vendor/autoload.php';

class ReportService {

    // ══════════════════════════════════════════════════════════════════════
    // INICIALIZACIÓN
    // ══════════════════════════════════════════════════════════════════════

    private $mpdf;

    public function __construct() {
        // mPDF necesita al menos 128 MB; el hosting compartido suele tener 64 MB por defecto
        $current = ini_get('memory_limit');
        if ((int)$current < 256) {
            @ini_set('memory_limit', '256M');
        }
        // Ampliar tiempo de ejecución para informes grandes
        @set_time_limit(120);

        $this->initMpdf();
    }

    private function initMpdf() {
        // Directorio temporal del sistema — siempre con permisos de escritura
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

        // Título/autor por defecto — cada generate*() los sobrescribe con el
        // nombre real del centro en cuanto conoce $cfg (aquí en el constructor
        // aún no ha llegado), así el visor de PDF nunca muestra "AulaPro Report"
        // en vez del centro que lo emite.
        $this->mpdf->SetTitle('Informe');
        $this->mpdf->SetAuthor('AulaPro');
    }

    // ══════════════════════════════════════════════════════════════════════
    // GENERACIÓN DE INFORMES
    // ══════════════════════════════════════════════════════════════════════

    public function generateBoletines($cfg, $ciclo, $estudiantes, $baseUrl = '') {
        $this->mpdf->SetTitle('Boletín de calificaciones — ' . ($cfg['nombreCentro'] ?? ''));
        $this->mpdf->SetAuthor($cfg['nombreCentro'] ?? 'AulaPro');
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
        $this->mpdf->SetTitle('Listado de alumnado — ' . ($cfg['nombreCentro'] ?? ''));
        $this->mpdf->SetAuthor($cfg['nombreCentro'] ?? 'AulaPro');
        $this->mpdf->AddPage('L');
        ob_start();
        include __DIR__ . '/../templates/pdf/listado.php';
        $html = ob_get_clean();
        $this->mpdf->WriteHTML($html);
        return $this->mpdf;
    }

    public function generateHorario($cfg, $ciclo, $celdas, $franjas, $dias) {
        $this->mpdf->SetTitle('Horario — ' . ($cfg['nombreCentro'] ?? ''));
        $this->mpdf->SetAuthor($cfg['nombreCentro'] ?? 'AulaPro');
        // Landscape with extra top margin for the 3-row header (accent + logos + info band)
        $this->mpdf->AddPage('L', '', '', '', '', '', 56, 20, 4, 3);
        ob_start();
        include __DIR__ . '/../templates/pdf/horario.php';
        $html = ob_get_clean();
        $this->mpdf->WriteHTML($html);
        return $this->mpdf;
    }

    // ══════════════════════════════════════════════════════════════════════
    // SALIDA
    // ══════════════════════════════════════════════════════════════════════

    public function stream($filename) {
        // Limpiar cualquier salida accidental antes de enviar el binario PDF
        if (ob_get_level()) ob_end_clean();
        $this->mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
    }
}
