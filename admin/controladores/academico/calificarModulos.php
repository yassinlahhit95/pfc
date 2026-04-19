<?php
session_start();
require_once "../../modelos/calificaciones.php";

if (isset($_POST['guardarNotas'])) {
    $idModulo = $_POST['idModulo'];
    
    // Arrays de notas que vienen del formulario
    $notas_1ev = $_POST['n1ev'];
    $notas_1f  = $_POST['n1f'];
    $notas_2ev = $_POST['n2ev'];
    $notas_2f  = $_POST['n2f'];

    $errorEncontrado = false;

    // Recorremos los alumnos (usando las IDs que son las llaves del array)
    foreach ($notas_1ev as $idAlumn => $valor) {
        $n1 = $notas_1ev[$idAlumn];
        $nf1 = $notas_1f[$idAlumn];
        $n2 = $notas_2ev[$idAlumn];
        $nf2 = $notas_2f[$idAlumn];

        // Validación simple: No permitir más de 10
        if ($n1 > 10) $n1 = 10;
        if ($nf1 > 10) $nf1 = 10;
        if ($n2 > 10) $n2 = 10;
        if ($nf2 > 10) $nf2 = 10;

        // Guardamos todo de forma simple
        if (!calificarModuloCompleto($idAlumn, $idModulo, $n1, $nf1, $n2, $nf2)) {
            $errorEncontrado = true;
        }
    }

    if ($errorEncontrado) {
        $_SESSION['error'] = "Hubo un error al guardar algunas notas.";
    } else {
        $_SESSION['exito'] = "Notas guardadas correctamente.";
    }

    header("Location: ../../vistas/academico/calificacionesModulos.php?idModulo=$idModulo");
    exit;
}

header("Location: ../../vistas/academico/calificacionesModulos.php");
exit;
?>
