<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

if (!isset($_POST['generarBoletin'])) {
    header("Location: ../../../vistas/admin/reportes/boletines.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idCiclo       = intval($_POST['idCiclo'] ?? 0);
$anioAcademico = trim($_POST['anioAcademico'] ?? '');
$idEstudiante  = intval($_POST['idEstudiante'] ?? 0);

$errores = [];

if ($idCiclo <= 0) {
    $errores['idCiclo'] = 'Debes seleccionar un ciclo formativo.';
}
if ($idEstudiante <= 0) {
    $errores['idEstudiante'] = 'Debes seleccionar un estudiante.';
}
if (empty($anioAcademico)) {
    $errores['anioAcademico'] = 'El año académico no puede estar vacío.';
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/admin/reportes/boletines.php");
    exit;
}

$datosCiclo = obtenerCicloPorId($idCiclo);

$estudiante = obtenerEstudiantePorId($idEstudiante);
$listaEstudiantes = $estudiante ? [$estudiante] : [];

if (empty($listaEstudiantes)) {
    header("Location: ../../../vistas/admin/reportes/boletines.php");
    exit;
}

// Logo como base64 para que funcione al imprimir
$rutaLogo = __DIR__ . '/../../../public/imagenes/aulapro.png';
$logoBase64 = '';
if (file_exists($rutaLogo)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($rutaLogo));
}

$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];
$fechaActual = date('d') . ' de ' . $meses[intval(date('m'))] . ' de ' . date('Y');
$nombreCiclo = $datosCiclo['nombreCiclo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletines de Notas — <?= $nombreCiclo ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .pagina-boletin {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 30px auto;
            padding: 15mm 20mm;
            box-shadow: 0 2px 20px rgba(0,0,0,0.12);
            page-break-after: always;
            position: relative;
        }

        .pagina-boletin:last-child {
            page-break-after: avoid;
        }

        .cabecera-boletin {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .cabecera-boletin img { height: 55px; width: auto; }
        .cabecera-boletin-texto { flex: 1; }

        .nombre-centro {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a6e;
            text-transform: uppercase;
        }

        .titulo-boletin {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a1a6e;
            background: #eef1fb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 18px;
        }

        .subtitulo-anio {
            text-align: center;
            font-size: 13px;
            color: #555;
            margin-top: 4px;
            margin-bottom: 18px;
        }

        .bloque-datos {
            background: #f8f9ff;
            border: 1px solid #dde3f5;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.9;
        }

        .bloque-datos strong { color: #1a1a6e; }

        .tabla-notas {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 16px;
        }

        .tabla-notas thead tr { background: #1a1a6e; color: white; }

        .tabla-notas th {
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
        }

        .tabla-notas th:first-child { text-align: left; }

        .tabla-notas td {
            padding: 7px 10px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
        }

        .tabla-notas td:first-child { text-align: left; }
        .tabla-notas tbody tr:nth-child(even) { background: #f8f9ff; }

        .titulo-seccion-boletin {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a6e;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #eef1fb;
            padding: 5px 10px;
            margin-bottom: 0;
            border-radius: 4px 4px 0 0;
        }

        .tabla-notas.tabla-retos thead tr { background: #2d6a4f; }
        .tabla-notas.tabla-tfg thead tr { background: #5a2d82; }

        .estado-aprobado { color: #2e7d32; font-weight: bold; }
        .estado-suspenso { color: #c62828; font-weight: bold; }
        .estado-pendiente { color: #888; }

        .bloque-resumen {
            border: 2px solid #1a1a6e;
            border-radius: 4px;
            padding: 14px 16px;
            margin-top: 20px;
            font-size: 13px;
        }

        .resumen-titulo {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a6e;
            text-transform: uppercase;
            border-bottom: 1px solid #dde3f5;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .resumen-fila {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #e0e0e0;
        }

        .nota-final-grande {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 12px;
            padding: 8px;
            border-radius: 4px;
        }

        .nota-final-aprobado { background: #e8f5e9; color: #2e7d32; }
        .nota-final-suspenso { background: #ffebee; color: #c62828; }
        .nota-final-pendiente { background: #f5f5f5; color: #757575; }


        .boton-imprimir {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 22px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .boton-imprimir:hover { background: #5a67d8; }

        @media print {
            body { background: white; padding: 0; }
            .pagina-boletin { box-shadow: none; margin: 0; width: 100%; }
            .boton-imprimir { display: none; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>

<button class="boton-imprimir" onclick="window.print()">
    &#128424; Imprimir / Guardar PDF
</button>

<?php foreach ($listaEstudiantes as $estudiante) {
    $idEstudianteActual = $estudiante['idEstudiante'];
    $nombreEstudiante   = mb_strtoupper($estudiante['nombreEstudiante'], 'UTF-8');
    $dniEstudiante      = $estudiante['dniEstudiante'];

    $listaNotas = listarCalificacionesPorEstudiante($idEstudianteActual);
    $listaRetos = listarCalificacionesRetoPorEstudiante($idEstudianteActual);
    $notaTFG    = obtenerCalificacionTFG($idEstudianteActual);

    // Calcular resumen de módulos
    $sumaNotasModulos = 0;
    $contadorModulos  = 0;
    $hayAlgunSuspenso = false;

    foreach ($listaNotas as $nota) {
        $mejor1 = max(floatval($nota['nota_1ev']), floatval($nota['nota_1final']));
        $mejor2 = max(floatval($nota['nota_2ev']), floatval($nota['nota_2final']));

        $evaluacionesConNota = 0;
        $sumaEvaluaciones    = 0;
        if ($nota['nota_1ev'] > 0 || $nota['nota_1final'] > 0) { $sumaEvaluaciones += $mejor1; $evaluacionesConNota++; }
        if ($nota['nota_2ev'] > 0 || $nota['nota_2final'] > 0) { $sumaEvaluaciones += $mejor2; $evaluacionesConNota++; }

        if ($evaluacionesConNota > 0) {
            $notaFinalModulo = $sumaEvaluaciones / $evaluacionesConNota;
            $sumaNotasModulos += $notaFinalModulo;
            $contadorModulos++;
            if ($notaFinalModulo < 5) {
                $hayAlgunSuspenso = true;
            }
        }
    }

    $promedioFinal = '-';
    $estadoGlobal  = 'PENDIENTE';
    $claseEstado   = 'nota-final-pendiente';

    if ($contadorModulos > 0) {
        $promedioFinal = round($sumaNotasModulos / $contadorModulos, 2);
        if ($promedioFinal >= 5 && !$hayAlgunSuspenso) {
            $estadoGlobal = 'APROBADO';
            $claseEstado  = 'nota-final-aprobado';
        } else {
            $estadoGlobal = 'SUSPENSO';
            $claseEstado  = 'nota-final-suspenso';
        }
    }
?>

<div class="pagina-boletin">

    <div class="cabecera-boletin">
        <?php if ($logoBase64) { ?>
            <img src="<?= $logoBase64 ?>" alt="Logo">
        <?php } ?>
        <div class="cabecera-boletin-texto">
            <div class="nombre-centro">AulaPro — Centro de Formación Profesional</div>
        </div>
    </div>

    <div class="titulo-boletin">Boletín Oficial de Calificaciones</div>
    <div class="subtitulo-anio">Curso Académico <?= $anioAcademico ?></div>

    <div class="bloque-datos">
        <div><strong>Alumno/a:</strong> <?= $nombreEstudiante ?></div>
        <div><strong>DNI / NIE / Pasaporte:</strong> <?= $dniEstudiante ?></div>
        <div><strong>Ciclo Formativo:</strong> <?= $nombreCiclo ?></div>
        <div><strong>Fecha de emisión:</strong> <?= $fechaActual ?></div>
    </div>

    <!-- MÓDULOS -->
    <div class="titulo-seccion-boletin">Calificaciones de Módulos</div>
    <table class="tabla-notas">
        <thead>
            <tr>
                <th>Módulo</th>
                <th>1ª Ev.</th>
                <th>1ª Final</th>
                <th>2ª Ev.</th>
                <th>2ª Final</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaNotas)) { ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#999; padding: 15px;">
                        No hay calificaciones de módulos registradas.
                    </td>
                </tr>
            <?php } else { ?>
                <?php foreach ($listaNotas as $nota) {
                    $mejor1 = max(floatval($nota['nota_1ev']), floatval($nota['nota_1final']));
                    $mejor2 = max(floatval($nota['nota_2ev']), floatval($nota['nota_2final']));

                    $evaluacionesConNota = 0;
                    $sumaEv = 0;
                    if ($nota['nota_1ev'] > 0 || $nota['nota_1final'] > 0) { $sumaEv += $mejor1; $evaluacionesConNota++; }
                    if ($nota['nota_2ev'] > 0 || $nota['nota_2final'] > 0) { $sumaEv += $mejor2; $evaluacionesConNota++; }

                    $notaDelModulo = $evaluacionesConNota > 0 ? round($sumaEv / $evaluacionesConNota, 2) : null;

                    if ($notaDelModulo === null) {
                        $estadoModulo = '<span class="estado-pendiente">Pendiente</span>';
                    } elseif ($notaDelModulo >= 5) {
                        $estadoModulo = '<span class="estado-aprobado">Aprobado</span>';
                    } else {
                        $estadoModulo = '<span class="estado-suspenso">Suspenso</span>';
                    }

                    $mostrar1ev    = $nota['nota_1ev'] > 0 ? $nota['nota_1ev'] : '—';
                    $mostrar1final = $nota['nota_1final'] > 0 ? $nota['nota_1final'] : '—';
                    $mostrar2ev    = $nota['nota_2ev'] > 0 ? $nota['nota_2ev'] : '—';
                    $mostrar2final = $nota['nota_2final'] > 0 ? $nota['nota_2final'] : '—';
                ?>
                <tr>
                    <td><?= $nota['nombreModulo'] ?></td>
                    <td><?= $mostrar1ev ?></td>
                    <td><?= $mostrar1final ?></td>
                    <td><?= $mostrar2ev ?></td>
                    <td><?= $mostrar2final ?></td>
                    <td><?= $estadoModulo ?></td>
                </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>

    <!-- RETOS -->
    <div class="titulo-seccion-boletin" style="background: #e8f5e9; color: #2d6a4f;">Calificaciones de Retos / Proyectos</div>
    <table class="tabla-notas tabla-retos">
        <thead>
            <tr>
                <th style="text-align: left;">Reto / Proyecto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Nota</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaRetos)) { ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:#999; padding: 12px;">
                        Sin calificaciones de retos registradas.
                    </td>
                </tr>
            <?php } else { ?>
                <?php foreach ($listaRetos as $reto) {
                    $notaReto = floatval($reto['nota']);
                    if ($notaReto >= 5) {
                        $estadoReto = '<span class="estado-aprobado">Aprobado</span>';
                    } else {
                        $estadoReto = '<span class="estado-suspenso">Suspenso</span>';
                    }
                ?>
                <tr>
                    <td><?= $reto['nombreReto'] ?></td>
                    <td><?= date('d/m/Y', strtotime($reto['fechaInicio'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($reto['fechaFin'])) ?></td>
                    <td><?= $notaReto ?></td>
                    <td><?= $estadoReto ?></td>
                </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>

    <!-- TFG -->
    <div class="titulo-seccion-boletin" style="background: #f3e8ff; color: #5a2d82;">Trabajo de Fin de Grado (TFG)</div>
    <table class="tabla-notas tabla-tfg">
        <thead>
            <tr>
                <th style="text-align: left;">Concepto</th>
                <th>Nota</th>
                <th>Estado</th>
                <th style="text-align: left;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$notaTFG) { ?>
                <tr>
                    <td colspan="4" style="text-align:center; color:#999; padding: 12px;">
                        Sin calificación de TFG registrada.
                    </td>
                </tr>
            <?php } else {
                $notaValorTFG = floatval($notaTFG['nota']);
                $estadoTFG = $notaValorTFG >= 5 ? '<span class="estado-aprobado">Aprobado</span>' : '<span class="estado-suspenso">Suspenso</span>';
            ?>
                <tr>
                    <td>TFG — Trabajo de Fin de Grado</td>
                    <td><?= $notaValorTFG ?></td>
                    <td><?= $estadoTFG ?></td>
                    <td><?= $notaTFG['observaciones'] ?? '—' ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- RESUMEN FINAL -->
    <div class="bloque-resumen">
        <div class="resumen-titulo">Resultado Final</div>
        <div class="resumen-fila">
            <span>Módulos calificados:</span>
            <span><?= $contadorModulos ?> de <?= count($listaNotas) ?></span>
        </div>
        <div class="resumen-fila">
            <span>Retos completados:</span>
            <span><?= count($listaRetos) ?></span>
        </div>
        <div class="resumen-fila">
            <span>Nota TFG:</span>
            <span><?= $notaTFG ? floatval($notaTFG['nota']) : '—' ?></span>
        </div>
        <div class="nota-final-grande <?= $claseEstado ?>">
            Nota Final Módulos: <?= $promedioFinal ?> — <?= $estadoGlobal ?>
        </div>
    </div>


</div>

<?php } ?>

</body>
</html>
