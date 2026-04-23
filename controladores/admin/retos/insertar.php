<?php
session_start();
require_once "../../../modelos/retos.php";
require_once "../../../modelos/modulos.php";

if (isset($_POST['insertarReto'])) {
    // Guardar datos en sesión para recuperarlos si hay error
    $_SESSION['datos_reto'] = $_POST;

    $nombre = trim($_POST['nombreReto']);
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    $horasReto = $_POST['horasReto'];
    $modulosSeleccionados = $_POST['modulos'];

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
    } else if (!is_numeric($horasReto)) {
        $_SESSION['error'] = "Las horas deben ser un número.";
    } else if (empty($modulosSeleccionados)) {
        $_SESSION['error'] = "Debe vincular al menos un módulo.";
    } else if (empty($fechaInicio) || empty($fechaFin)) {
        $_SESSION['error'] = "Las fechas son obligatorias.";
    } else {
        // --- CÁLCULO DE HORAS DISPONIBLES POR FECHA ---
        // 6 horas/día, Lunes a Viernes
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        $diasLaborales = 0;

        while ($inicio <= $fin) {
            $diaSemana = $inicio->format('N'); // 1 (Lunes) a 7 (Domingo)
            if ($diaSemana <= 5) {
                $diasLaborales++;
            }
            $inicio->modify('+1 day');
        }

        $horasMaximasPermitidas = $diasLaborales * 6;

        if ($horasReto > $horasMaximasPermitidas) {
            $_SESSION['error'] = "En el periodo elegido solo hay " . $horasMaximasPermitidas . "h disponibles (6h/día laborable). No puede asignar " . $horasReto . "h.";
        } else {
            // Validar horas disponibles en cada módulo (BD)
            $errorModulo = "";
            foreach ($modulosSeleccionados as $idModulo) {
                $modulo = obtenerModuloPorId($idModulo);
                $horasUsadas = obtenerHorasTotalesRetosModulo($idModulo);
                $disponibles = $modulo['horasMaximas'] - $horasUsadas;
                
                if ($horasReto > $disponibles) {
                    $errorModulo = "El módulo " . $modulo['nombreModulo'] . " solo tiene " . $disponibles . "h libres.";
                    break;
                }
            }

            if (!empty($errorModulo)) {
                $_SESSION['error'] = $errorModulo;
            } else {
                $idReto = insertarReto($nombre, $fechaInicio, $fechaFin, $horasReto);
                if ($idReto) {
                    foreach ($modulosSeleccionados as $idModulo) {
                        asociarModuloReto($idModulo, $idReto);
                    }
                    $_SESSION['exito'] = "Reto creado correctamente.";
                    unset($_SESSION['datos_reto']); // Limpiar si todo fue bien
                    header("Location: /pfc/vistas/admin/retos/verRetos.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Error al guardar el reto.";
                }
            }
        }
    }
    header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    exit;
}

header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;
?>