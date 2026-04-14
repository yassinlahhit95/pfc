<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/anuncios.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloAnuncio = new anuncio($conexionBD);

if (isset($_POST['guardarAnuncio'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores'], $_SESSION['datos_anuncios']);

    if ($accion == 'insertar') {
        $errores = [];
        $titulo = trim($_POST['titulo'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');
        $fechaExp = trim($_POST['fecha_expiracion'] ?? '');

        if (empty($titulo)) {
            $errores['titulo'] = "El título es obligatorio.";
        }
        if (empty($mensaje)) {
            $errores['mensaje'] = "El mensaje es obligatorio.";
        }
        if (empty($fechaExp)) {
            $errores['fecha_expiracion'] = "La fecha de expiración es obligatoria.";
        }

        if (count($errores) > 0) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_anuncios'] = $_POST;
            header("Location: ../vistas/anuncios/gestionAnuncios.php");
            exit;
        }

        if ($modeloAnuncio->insertarAnuncio($titulo, $mensaje, $fechaExp)) {
            $_SESSION['exito'] = "Anuncio publicado correctamente.";
        }
        header("Location: ../vistas/anuncios/gestionAnuncios.php");
        exit;
    }
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['id'];
    if ($modeloAnuncio->eliminarAnuncio($id)) {
        $_SESSION['exito'] = "Anuncio eliminado.";
    }
    header("Location: ../vistas/anuncios/gestionAnuncios.php");
    exit;
}
?>