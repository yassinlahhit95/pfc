<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$idCiclo = (int)($_GET['idCiclo'] ?? 0);
$anioEstudio = trim($_GET['anioEstudio'] ?? '');
$sistema = trim($_GET['sistema'] ?? 'euskadi_hezigune');

if ($idCiclo <= 0) {
    die("Ciclo formativo no especificado.");
}

$ciclo = obtenerCicloPorId($idCiclo);
if (!$ciclo) {
    die("Ciclo no encontrado.");
}

// Fetch all modules of this cycle (filtered by year if specified)
$modulos = listarModulosPorCiclo($idCiclo);
if (!empty($anioEstudio)) {
    $modulos = array_values(array_filter($modulos, function ($m) use ($anioEstudio) {
        return trim($m['cursoAnio']) === $anioEstudio;
    }));
}

// Fetch all students in this cycle (filtered by year if specified)
$con = obtenerConexion();
$sqlEst = "SELECT e.idEstudiante, e.nombreEstudiante, e.dniEstudiante, e.emailEstudiante
           FROM estudiantes e
           WHERE e.idCiclo = ? AND e.activo = 1";
if (!empty($anioEstudio)) {
    $sqlEst .= " AND e.anioEstudio = ?";
}
$stmtEst = mysqli_prepare($con, $sqlEst);
if (!empty($anioEstudio)) {
    mysqli_stmt_bind_param($stmtEst, "is", $idCiclo, $anioEstudio);
} else {
    mysqli_stmt_bind_param($stmtEst, "i", $idCiclo);
}
mysqli_stmt_execute($stmtEst);
$resEst = mysqli_stmt_get_result($stmtEst);
$estudiantes = [];
while ($row = mysqli_fetch_assoc($resEst)) {
    $estudiantes[] = $row;
}

// Fetch school center configuration code
$resCC = mysqli_query($con, "SELECT codigoCentro, cursoEscolar FROM configuracion_centro LIMIT 1");
$cc = mysqli_fetch_assoc($resCC);
$codigoCentro = $cc['codigoCentro'] ?? 'CENTRO001';
$cursoEscolar = $cc['cursoEscolar'] ?? date('Y') . '-' . (date('Y') + 1);

// Generate XML content based on region
if ($sistema === 'euskadi_hezigune') {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><HeziguneExport/>');
    $xml->addAttribute('codigoCentro', $codigoCentro);
    $xml->addAttribute('ciclo', $ciclo['abreviaturaCiclo'] ?? $ciclo['nombreCiclo']);
    $xml->addAttribute('cursoEscolar', $cursoEscolar);
    $xml->addAttribute('fechaGeneracion', date('Y-m-d'));

    $alumnosNode = $xml->addChild('Alumnos');

    foreach ($estudiantes as $est) {
        $alumnoNode = $alumnosNode->addChild('Alumno');
        $alumnoNode->addAttribute('dni', $est['dniEstudiante']);
        $alumnoNode->addAttribute('nombre', mb_strtoupper($est['nombreEstudiante'], 'UTF-8'));

        $califsNode = $alumnoNode->addChild('Calificaciones');

        // Get final grades calculated dynamically for this student
        $resultados = obtenerResultadosFinalesEstudiante((int)$est['idEstudiante'], $modulos);

        foreach (($resultados['detalles_modulos'] ?? []) as $det) {
            $califNode = $califsNode->addChild('Calificacion');
            $califNode->addAttribute('modulo', $det['nombreModulo']);
            
            // Standardize final note (1-10 integer for Basque Country acts)
            $nota = $det['nota_final'];
            if ($nota === '-' || $nota === '') {
                $notaVal = 'NP'; // No presentado / Pendiente
            } else {
                $notaVal = (string)round((float)$nota);
            }
            
            $califNode->addAttribute('nota', $notaVal);
            $califNode->addAttribute('estado', $det['estado'] ?? 'Pendiente');
        }
    }

    // Format output with spacing
    $dom = new DOMDocument("1.0");
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    
    $filename = "Hezigune_" . ($ciclo['abreviaturaCiclo'] ?? 'Ciclo') . "_" . (!empty($anioEstudio) ? $anioEstudio : 'Todos') . "_" . date('Ymd_His') . ".xml";

    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo $dom->saveXML();
    exit;
} else {
    die("Sistema regional no soportado.");
}
