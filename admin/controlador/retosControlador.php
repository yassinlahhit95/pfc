<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/retos.php";
require_once "../modelos/modulos.php";

$con = new Conexion();
$conexion = $con->conectar();
$modeloReto = new reto($conexion);
$modeloModulo = new modulo($conexion);

if (isset($_POST['guardarReto'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_reto']);

    $nombre = trim($_POST['nombreReto']);
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    $horas = trim($_POST['horasReto']);
    $modulosSeleccionados = $_POST['modulos'] ?? [];
    $errores = [];

    // Basic required field validation
    if (!isset($_POST['nombreReto']) || empty($nombre)) {
        $errores['nombreReto'] = "El nombre del reto es obligatorio";
    }

    if (!isset($_POST['fechaInicio']) || empty($fechaInicio)) {
        $errores['fechaInicio'] = "La fecha de inicio es obligatoria";
    }

    if (!isset($_POST['fechaFin']) || empty($fechaFin)) {
        $errores['fechaFin'] = "La fecha de fin es obligatoria";
    }

    if (!isset($_POST['horasReto']) || empty($horas) || $horas <= 0) {
        $errores['horasReto'] = "Las horas deben ser un número positivo";
    }

    if (empty($modulosSeleccionados)) {
        $errores['modulos'] = "Debes seleccionar al menos un módulo";
    }

    // If there are basic errors, stop and redirect
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_reto'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarRetos.php" : "modificarRetos.php?id=" . $_POST['idReto'];
        header("Location: ../vistas/retos/" . $url);
        exit;
    }

    // Business Logic: Check hour constraints for each selected module
    $horas = intval($horas);
    foreach ($modulosSeleccionados as $idModulo) {
        $moduloInfo = $modeloModulo->obtenerModuloPorIdModelo($idModulo);
        $horasActuales = $modeloModulo->obtenerHorasTotalesRetosModulo($idModulo);
        
        if ($accion == 'actualizar') {
            $retoActual = $modeloReto->obtenerRetoPorIdModelo($_POST['idReto']);
            $modulosDeReto = $modeloReto->obtenerModulosDeReto($_POST['idReto']);
            $yaEstaba = false;
            foreach($modulosDeReto as $m) {
                if($m['idModulo'] == $idModulo) $yaEstaba = true;
            }
            if ($yaEstaba) {
                $horasActuales -= $retoActual['horasReto'];
            }
        }

        if (($horasActuales + $horas) > $moduloInfo['horasMaximas']) {
            $errores['horasReto'] = "El módulo " . $moduloInfo['nombreModulo'] . " superaría las " . $moduloInfo['horasMaximas'] . " horas (Actual: $horasActuales, Nuevo: $horas)";
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_reto'] = $_POST;
            $url = ($accion == 'insertar') ? "agregarRetos.php" : "modificarRetos.php?id=" . $_POST['idReto'];
            header("Location: ../vistas/retos/" . $url);
            exit;
        }
    }

    $datos = [
        'nombreReto' => $nombre,
        'fechaInicio' => $fechaInicio,
        'fechaFin' => $fechaFin,
        'horasReto' => $horas
    ];

    if ($accion == 'insertar') {
        $idReto = $modeloReto->insertarRetoModelo($datos);
        if ($idReto) {
            foreach ($modulosSeleccionados as $idModulo) {
                $modeloReto->asociarModuloReto($idModulo, $idReto);
            }
            $_SESSION['exito'] = "Reto creado";
        }
    } else {
        $idReto = $_POST['idReto'];
        $datos['idReto'] = $idReto;
        $modeloReto->actualizarRetoModelo($datos);
        
        $modulosActuales = $modeloReto->obtenerModulosDeReto($idReto);
        $idsActuales = array_column($modulosActuales, 'idModulo');
        
        foreach ($idsActuales as $idAct) {
            if (!in_array($idAct, $modulosSeleccionados)) {
                $modeloReto->desvincularModuloReto($idAct, $idReto);
            }
        }
        foreach ($modulosSeleccionados as $idMod) {
            if (!in_array($idMod, $idsActuales)) {
                $modeloReto->asociarModuloReto($idMod, $idReto);
            }
        }
        $_SESSION['exito'] = "Reto actualizado";
    }

    header("Location: ../vistas/retos/verRetos.php");
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        $id = $_POST['idReto'];
        $modeloReto->eliminarRetoModelo($id);
        $_SESSION['exito'] = "Reto borrado";
        header("Location: ../vistas/retos/verRetos.php");
        exit;
    }

    if ($accion == 'calificar') {
        $idReto = $_POST['idReto'];
        $notas = $_POST['notas']; // Array [idEstudiante => nota]
        foreach ($notas as $idEstudiante => $nota) {
            if ($nota !== "") {
                $modeloReto->calificarRetoEstudiante($idEstudiante, $idReto, floatval($nota));
            }
        }
        $_SESSION['exito'] = "Calificaciones guardadas";
        header("Location: ../vistas/retos/calificarReto.php?id=" . $idReto);
        exit;
    }
}
?>
