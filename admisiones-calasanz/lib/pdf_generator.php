<?php
require_once(__DIR__ . '/fpdf.php');

class PDF_Aceptacion extends FPDF {
    function Header() {
        // Logo a la derecha
        if (file_exists('img/logo.png')) {
            $this->Image('img/logo.png', 160, 10, 40);
        }
        
        // Título a la izquierda
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(100, 10, utf8_decode('Formulario de aceptación'), 0, 1, 'L');
        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

function generarPDF($datos, $db_conn) {

    // Buscar datos del ciclo
    $stmt = $db_conn->prepare("SELECT * FROM precios_ciclos WHERE id_ciclo = ?");
    $stmt->bind_param("i", $datos['ciclo']);
    $stmt->execute();
    $result = $stmt->get_result();
    $ciclo_data = $result->fetch_assoc();
    
    $nombre_ciclo = $ciclo_data ? $ciclo_data['nombre_ciclo'] : "Desconocido";
    $num_recibos = $ciclo_data ? $ciclo_data['num_recibos'] : "0";
    $precio_mensual = $ciclo_data ? number_format($ciclo_data['precio_mensual'], 2, ',', '.') : "0,00";
    $pago_inicial = $ciclo_data ? number_format($ciclo_data['pago_inicial'], 2, ',', '.') : "0,00";

    $pdf = new PDF_Aceptacion();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);

    // Texto de introducción
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 5, utf8_decode("Al realizar la matrícula para el presente curso escolar en CALASANZ LANBIDE IKASTEGIA, me comprometo voluntariamente a:"), 0, 'L');
    $pdf->Ln(3);
    $pdf->SetFont('Arial', '', 10);

    $items = [
        "   1. Aceptar y respetar el Proyecto Educativo del Centro (PEC).",
        "   2. Aceptar el Reglamento de Régimen Interno (normativa que regula la vida en nuestro Centro).",
        "   3. Aceptar las actividades complementarias y/o extraescolares que el centro, de acuerdo con el Reglamento de Conciertos Educativos, pueda establecer y siempre que las mismas sean aprobadas por el Consejo Escolar del centro.",
        "   4. Aceptar cuantas normas académicas pudieran surgir del Consejo Escolar y aquellos acuerdos del mismo que afecten a servicios complementarios ofrecidos en el centro.",
        "   5. Al formalizar la matrícula, se abonará la mensualidad de septiembre y el seguro escolar, así como la plataforma de aprendizaje online de Office 365. En caso de baja antes del comienzo del periodo lectivo, siempre que se cubra la plaza que se deja vacante, se devolverá el importe del seguro escolar y de las plataformas digitales correspondientes y el 50% del importe de la mensualidad, asumiendo el 50% restante en concepto de gastos de tramitación.",
        "   6. Condiciones Económicas: " . $nombre_ciclo . " consta de " . $num_recibos . " recibos de " . $precio_mensual .  " Euros al mes, excepto la primera mensualidad que se paga en efectivo al formalizar la matrícula junto a las licencias digitales y seguros, y será de un total de " . $pago_inicial ." Euros."
    ];

    foreach ($items as $item) {
        $pdf->MultiCell(0, 5, utf8_decode($item), 0, 'L');
        $pdf->Ln(2);
    }

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'I', 8);
    $text_lopd = "*Los datos facilitados por Vd. se incluirán en un fichero responsabilidad de CALASANZ SANTURTZI SOCIEDAD LIMITADA, y podrán ser cedidos a las Consejerías de Educación, Empleo y Políticas Sociales del Gobierno Vasco y otros organismos oficiales, para la correcta gestión de los servicios solicitados. Siempre y cuando se cumplan los requisitos exigidos por la normativa, usted podrá ejercer sus derechos de acceso, rectificación, limitación de tratamiento, supresión (\"derecho al olvido\"), portabilidad, oposición y revocación, en los términos que establece la normativa vigente y aplicable en materia de protección de datos, dirigiendo su petición a la dirección postal C/ HOSPITAL BAJO 11 48980, SANTURTZI (BIZKAIA) o bien a través de correo electrónico lopd@calasanz.eus.";
    $pdf->MultiCell(0, 4, utf8_decode($text_lopd), 0, 'L');
    
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);
    $texto_autorizacion = "Mediante la firma de este documento autorizo a CALASANZ SANTURTZI a publicar las fotos obtenidas en los eventos realizados y organizados por CALASANZ LANBIDE IKASTEGIA, en los medios de difusión o comunicación que el centro establezca.";
    $pdf->MultiCell(0, 5, utf8_decode($texto_autorizacion), 0, 'L');

    $pdf->Ln(5);
    // Cuadro de datos del solicitante
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 10, utf8_decode('DATOS DEL SOLICITANTE:'), 'T', 1);
    $pdf->SetFont('Arial', '', 10);
    
    $pdf->Cell(50, 7, utf8_decode('Nombre:'), 0, 0);
    $pdf->Cell(0, 7, utf8_decode($datos['nombre']), 0, 1);
    
    $pdf->Cell(50, 7, utf8_decode('Apellidos:'), 0, 0);
    $pdf->Cell(0, 7, utf8_decode($datos['apellidos']), 0, 1);
    
    $pdf->Cell(50, 7, utf8_decode('DNI o NIE:'), 0, 0);
    $pdf->Cell(0, 7, utf8_decode($datos['dni']), 0, 1);
    
    $pdf->Cell(50, 7, utf8_decode('Ciclo formativo:'), 0, 0);
    $pdf->Cell(0, 7, utf8_decode($nombre_ciclo), 0, 1);

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $confirmacion = "He leído, comprendo y acepto los términos y condiciones económicas para la realización de la matrícula en CALASANZ LANBIDE IKASTEGIA.";
    $pdf->MultiCell(0, 5, utf8_decode($confirmacion), 0, 'L');

    $pdf->Ln(5);
    $fecha_actual = date('d/m/Y H:i:s');
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 10, utf8_decode('Documento generado el: ') . $fecha_actual, 0, 1, 'R');

    // Guardar archivo
    $safe_name = preg_replace('/[^A-Za-z0-9_]/', '', $datos['nombre'] . '_' . $datos['apellidos']);
    $safe_dni = preg_replace('/[^A-Za-z0-9]/', '', $datos['dni']);
    $filename = 'aceptacion_' . $safe_name . '_' . $safe_dni . '_' . time() . '.pdf';
    $filepath = 'pdf_aceptacion/' . $filename;
    
    $pdf->Output('F', $filepath);
    return $filepath;
}
?>
