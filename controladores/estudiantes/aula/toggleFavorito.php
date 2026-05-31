<?php
// Marca / desmarca un recurso como favorito (#9) y vuelve a la página de origen.
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (empty($_SESSION['idEstudiante'])) { header("Location: ../../../vistas/login.php"); exit; }

$idEstudiante = $_SESSION['idEstudiante'];
$idArchivo    = intval($_GET['idArchivo'] ?? 0);
$origen       = $_GET['origen'] ?? 'recursos';   // recursos | favoritos
$idModulo     = intval($_GET['idModulo'] ?? 0);
$carpeta      = intval($_GET['carpeta'] ?? 0);

if ($idArchivo > 0) {
    // El archivo debe existir y pertenecer al ciclo del estudiante
    $archivo = obtenerArchivoPorId($idArchivo);
    $datos   = obtenerEstudiantePorId($idEstudiante);
    $modulo  = $archivo ? obtenerModuloPorId($archivo['idModulo']) : null;

    if ($archivo && $modulo && $modulo['idCiclo'] == ($datos['idCiclo'] ?? -1)) {
        if (esFavoritoAula($idEstudiante, $idArchivo)) {
            quitarFavoritoAula($idEstudiante, $idArchivo);
            $_SESSION['exito'] = "Recurso quitado de favoritos.";
        } else {
            marcarFavoritoAula($idEstudiante, $idArchivo);
            $_SESSION['exito'] = "Recurso añadido a favoritos.";
        }
    } else {
        $_SESSION['errores'] = "No tienes permiso sobre este recurso.";
    }
}

// Destino seguro (construido en el servidor, sin URLs arbitrarias)
if ($origen === 'favoritos') {
    $destino = "../../../vistas/estudiantes/aula/favoritos.php";
} else {
    $destino = "../../../vistas/estudiantes/aula/recursos.php?id=$idModulo";
    if ($carpeta) $destino .= "&carpeta=$carpeta";
}
header("Location: $destino");
exit;
