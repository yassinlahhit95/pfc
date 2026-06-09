<?php
require_once __DIR__ . "/../../../include/Security.php";
// Mueve una carpeta a otra carpeta (cambia su padre). Solo el propietario. POST + CSRF.
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = "La sesión ha caducado. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/aula/recursos.php?id=" . intval($_POST['modulo'] ?? 0));
    exit;
}

$idProfesor = $_SESSION['idProfesor'];
$id         = intval($_POST['id'] ?? 0);              // carpeta a mover
$destino    = intval($_POST['destino'] ?? 0) ?: null; // nuevo padre (0 = raíz)
$idModulo   = intval($_POST['modulo'] ?? 0);
$regresar   = intval($_POST['regresar'] ?? 0);        // carpeta actual a la que volver

if ($id > 0) {
    $carpeta = obtenerCarpetaAulaPorId($id);
    if ($carpeta && $carpeta['idProfesor'] == $idProfesor) {
        if (moverCarpetaAula($id, $destino)) {
            $_SESSION['exito'] = "Carpeta movida.";
        } else {
            $_SESSION['errores'] = "No se pudo mover la carpeta a ese destino.";
        }
        $idModulo = $idModulo ?: $carpeta['idModulo'];
    }
}

$dest = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($regresar) $dest .= "&carpeta=$regresar";
header("Location: $dest");
exit;
