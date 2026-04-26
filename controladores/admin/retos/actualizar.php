<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $id_reto = $_POST['idReto'];
    $nombre = trim($_POST['nombreReto']);
    $horas = $_POST['horasReto'];
    $inicio = $_POST['fechaInicioReto'];
    $fin = $_POST['fechaFinReto'];
    $modulos = [];
    if (isset($_POST['modulosReto'])) { $modulos = $_POST['modulosReto']; }

    $lista_de_errores = [];

    if (empty($nombre)) {
        $lista_de_errores['nombreReto'] = "El nombre es obligatorio.";
    }
    if (empty($horas)) {
        $lista_de_errores['horasReto'] = "Las horas son obligatorias.";
    } else {
        if (!is_numeric($horas)) {
            $lista_de_errores['horasReto'] = "Las horas deben ser un número.";
        }
    }
    if (empty($inicio)) {
        $lista_de_errores['fechaInicioReto'] = "La fecha de inicio es obligatoria.";
    }
    if (empty($fin)) {
        $lista_de_errores['fechaFinReto'] = "La fecha de fin es obligatoria.";
    }
    if (empty($modulos)) {
        $lista_de_errores['modulosReto'] = "Debe seleccionar al menos un módulo.";
    } else if (is_numeric($horas)) {
        // Validar que el módulo tenga suficientes horas disponibles
        foreach ($modulos as $idModulo) {
            if (!comprobarHorasDisponiblesModulo($idModulo, $horas, $id_reto)) {
                $lista_de_errores['modulosReto'] = "Uno de los módulos seleccionados no tiene suficientes horas disponibles (sobrepasa el límite del módulo).";
                break;
            }
        }
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarReto($id_reto, $nombre, $inicio, $fin, $horas, $modulos);
        if ($resultado) {
            $_SESSION['exito'] = "Reto actualizado correctamente.";
            header("Location: /pfc/vistas/admin/retos/verRetos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_reto'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/retos/modificarRetos.php?idReto=$id_reto");
    exit;
}

header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;

