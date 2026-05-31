<?php
// Generar / revocar enlaces temporales de compartición de recursos (#14).
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idProfesor = $_SESSION['idProfesor'];
$accion     = $_POST['accion'] ?? 'crear'; // crear | revocar
$idModulo   = intval($_POST['idModulo'] ?? 0);

if ($accion === 'crear') {
    $idArchivo = intval($_POST['idArchivo'] ?? 0);
    $archivo   = $idArchivo > 0 ? obtenerArchivoPorId($idArchivo) : null;

    if (!$archivo || $archivo['idProfesor'] != $idProfesor) {
        $_SESSION['errores'] = "No puedes compartir este archivo.";
    } else {
        $idModulo         = $idModulo ?: $archivo['idModulo'];
        $permitirDescarga = !empty($_POST['permitirDescarga']) ? 1 : 0;
        $dias             = intval($_POST['dias'] ?? 0); // 0 = sin caducidad
        $fechaExpiracion  = $dias > 0 ? date('Y-m-d H:i:s', strtotime("+$dias days")) : null;

        $token = crearEnlaceCompartidoAula($idArchivo, $idProfesor, $fechaExpiracion, $permitirDescarga);
        if ($token) {
            // Construir URL pública absoluta
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $base   = $scheme . '://' . $_SERVER['HTTP_HOST'];
            $raiz   = preg_replace('#/controladores/profesores/aula/?$#', '', dirname($_SERVER['PHP_SELF']));
            $url    = $base . $raiz . '/controladores/aula/compartido.php?token=' . $token;
            $cad    = $fechaExpiracion ? ' (caduca el ' . date('d/m/Y', strtotime($fechaExpiracion)) . ')' : ' (sin caducidad)';
            $_SESSION['exito'] = "Enlace de compartición creado" . $cad . ": " . $url;
        } else {
            $_SESSION['errores'] = "No se pudo crear el enlace.";
        }
    }
} elseif ($accion === 'revocar') {
    $idEnlace = intval($_POST['idEnlace'] ?? 0);
    if (desactivarEnlaceCompartidoAula($idEnlace, $idProfesor)) {
        $_SESSION['exito'] = "Enlace revocado.";
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if (!empty($archivo['idCarpeta'])) $destino .= "&carpeta=" . $archivo['idCarpeta'];
header("Location: $destino");
exit;
