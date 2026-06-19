<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idProfesor = $_SESSION['idProfesor'];
$idModulo   = intval($_POST['idModulo'] ?? 0);
$nombre     = trim($_POST['nombre'] ?? '');
$color      = trim($_POST['color'] ?? '#0ea5e9');
$icono      = trim($_POST['icono'] ?? 'fa-folder');
$idPadre    = intval($_POST['idPadre'] ?? 0) ?: null;

// Sanitizar color hexadecimal e icono (FontAwesome) para evitar valores arbitrarios
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#0ea5e9';
if (!preg_match('/^fa-[a-z0-9-]+$/', $icono))    $icono = 'fa-folder';

// La carpeta padre debe pertenecer al mismo módulo (evita mover carpetas entre módulos)
if ($idPadre) {
    $padre = obtenerCarpetaAulaPorId($idPadre);
    if (!$padre || $padre['idModulo'] != $idModulo) $idPadre = null;
}

if ($idModulo > 0 && !empty($nombre)) {
    if (insertarCarpetaAula($nombre, $idModulo, $idProfesor, $color, $icono, $idPadre)) {
        $_SESSION['exito'] = "La carpeta ha sido creada correctamente.";
    } else {
        $_SESSION['errores'] = "No se pudo crear la carpeta.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($idPadre) $destino .= "&carpeta=$idPadre";
header("Location: $destino");
exit;
