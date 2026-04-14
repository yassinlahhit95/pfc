<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/anuncios.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloAnuncio = new anuncio($conexionBD);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores'], $_SESSION['datos_viejos']);

    if ($accion == 'insertar') {
        $errores = [];
        $titulo = trim($_POST['titulo']);
        $mensaje = trim($_POST['mensaje']);
        $fechaExp = trim($_POST['fecha_expiracion']);

        if (empty($titulo)) {
            $errores['titulo'] = "El título es necesario.";
        }
        if (empty($mensaje)) {
            $errores['mensaje'] = "El contenido del mensaje es obligatorio.";
        }
        if (empty($fechaExp)) {
            $errores['fecha_expiracion'] = "Debe indicar cuándo expira el anuncio.";
        }

        if (count($errores) > 0) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_viejos'] = $_POST;
            header("Location: ../vistas/anuncios/gestionAnuncios.php");
            exit;
        }

        if ($modeloAnuncio->insertarAnuncio($titulo, $mensaje, $fechaExp)) {
            $_SESSION['exito'] = "Anuncio publicado en el panel.";
        }
        header("Location: ../vistas/anuncios/gestionAnuncios.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['id'];
        if ($modeloAnuncio->eliminarAnuncio($id)) {
            $_SESSION['exito'] = "Anuncio retirado.";
        }
        header("Location: ../vistas/anuncios/gestionAnuncios.php");
        exit;
    }
}
?>