<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../vendor/autoload.php';

// ══════════════════════════════════════════════════════════════════════
// GENERACIÓN DE PDF
// ══════════════════════════════════════════════════════════════════════
function generarPDFAceptacion($datos) {
    try {
        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 20,
            'margin_top'    => 30,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10,
        ]);

        $mpdf->SetTitle("Formulario de Aceptación - AulaPro");
        $mpdf->SetAuthor("AulaPro Sistema Académico");

        // ── Cabecera y pie de página ──
        $header = '
        <table width="100%" style="border-bottom: 1px solid #000000; vertical-align: bottom; font-family: serif; font-size: 9pt; color: #000088;">
            <tr>
                <td width="33%"><span style="font-weight: bold; font-size: 14pt;">AulaPro</span></td>
                <td width="33%" align="center"></td>
                <td width="33%" style="text-align: right;"><span style="font-weight: bold;">DOCUMENTO OFICIAL</span></td>
            </tr>
        </table>';

        $footer = '
        <table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
            <tr>
                <td width="33%"><span style="font-weight: bold; font-style: italic;">{DATE j-m-Y}</span></td>
                <td width="33%" align="center" style="font-weight: bold; font-style: italic;">Página {PAGENO}/{nbpg}</td>
                <td width="33%" style="text-align: right; ">AulaPro v2.0</td>
            </tr>
        </table>';

        $mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLFooter($footer);

        $html = '
        <style>
            body { font-family: "Helvetica", sans-serif; color: #333; }
            h1 { color: #252260; text-align: center; font-size: 22px; margin-bottom: 30px; }
            .section-title { font-weight: bold; font-size: 14px; border-bottom: 2px solid #4f46e5; padding-bottom: 5px; margin-bottom: 15px; color: #4f46e5; }
            .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .data-table td { padding: 8px; border: 1px solid #eee; }
            .label { font-weight: bold; width: 30%; background-color: #f9fafb; }
            .legal-text { font-size: 10px; text-align: justify; line-height: 1.4; color: #666; margin-top: 20px; }
            .signature-box { margin-top: 50px; border-top: 1px solid #ccc; width: 200px; text-align: center; padding-top: 10px; }
        </style>

        <h1>FORMULARIO DE ACEPTACIÓN Y PRE-MATRÍCULA</h1>

        <div class="section-title">DATOS DEL SOLICITANTE</div>
        <table class="data-table">
            <tr><td class="label">Nombre completo:</td><td>' . htmlspecialchars($datos['nombre'] . ' ' . $datos['apellidos']) . '</td></tr>
            <tr><td class="label">DNI / NIE:</td><td>' . htmlspecialchars($datos['dni']) . '</td></tr>
            <tr><td class="label">Email:</td><td>' . htmlspecialchars($datos['email']) . '</td></tr>
            <tr><td class="label">Teléfono:</td><td>' . htmlspecialchars($datos['telefono'] ?? 'N/A') . '</td></tr>
        </table>

        <div class="section-title">DATOS ACADÉMICOS</div>
        <table class="data-table">
            <tr><td class="label">Ciclo Formativo:</td><td>' . htmlspecialchars($datos['ciclo_nombre']) . '</td></tr>
            <tr><td class="label">Curso solicitado:</td><td>' . htmlspecialchars($datos['curso']) . '</td></tr>
        </table>

        <div class="section-title">DATOS DEL TUTOR LEGAL</div>
        <table class="data-table">
            <tr><td class="label">Nombre del Tutor:</td><td>' . htmlspecialchars($datos['nombreTutor']) . '</td></tr>
            <tr><td class="label">DNI del Tutor:</td><td>' . htmlspecialchars($datos['dniTutor']) . '</td></tr>
            <tr><td class="label">Parentesco:</td><td>' . htmlspecialchars($datos['parentescoTutor']) . '</td></tr>
        </table>

        <div class="section-title">CLÁUSULAS Y COMPROMISOS</div>
        <ol style="font-size: 11px;">
            <li>El solicitante (y su tutor legal en caso de minoría de edad) se compromete a respetar las normas de convivencia y el reglamento interno del centro.</li>
            <li>Se acepta que los datos facilitados sean tratados con fines exclusivamente educativos y administrativos, conforme al RGPD.</li>
            <li>El solicitante declara que toda la información aportada es veraz y se compromete a entregar la documentación física necesaria en el plazo establecido por el centro.</li>
            <li>En caso de admisión, el solicitante recibirá las credenciales de acceso al portal AulaPro a través del correo electrónico facilitado.</li>
        </ol>

        <div class="legal-text">
            * De conformidad con lo dispuesto en el Reglamento General de Protección de Datos (RGPD) y la Ley Orgánica 3/2018 (LOPDGDD), le informamos que sus datos serán tratados por el centro para la gestión de su solicitud. Puede ejercer sus derechos de acceso, rectificación, supresión y otros derechos legales contactando con la administración del centro.
        </div>

        <table width="100%" style="margin-top: 40px;">
            <tr>
                <td width="50%">
                    <div class="signature-box">Firma del Solicitante</div>
                </td>
                <td width="50%" align="right">
                    <div class="signature-box">Firma del Tutor Legal</div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 30px; text-align: center; font-size: 9px; color: #999;">
            Documento generado digitalmente por AulaPro. Identificador: ' . md5($datos['dni'] . time()) . '
        </div>
        ';

        $mpdf->WriteHTML($html);

        // ══════════════════════════════════════════════════════════════════════
        // SALIDA
        // ══════════════════════════════════════════════════════════════════════
        $destDir = __DIR__ . '/../../public/uploads/admisiones/documentos/';
        if (!is_dir($destDir)) mkdir($destDir, 0777, true);

        $fileName = 'aceptacion_' . preg_replace('/[^A-Za-z0-9]/', '', $datos['dni']) . '_' . time() . '.pdf';
        $filePath = $destDir . $fileName;

        $mpdf->Output($filePath, 'F');

        return '/public/uploads/admisiones/documentos/' . $fileName;

    } catch (\Exception $e) {
        error_log("Error generando PDF: " . $e->getMessage());
        return false;
    }
}
