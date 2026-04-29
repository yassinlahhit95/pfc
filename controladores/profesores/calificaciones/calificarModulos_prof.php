<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['guardarNotas'])) {
    $idMod = $_POST['idModulo'];
    $idCiclo = $_POST['idCiclo']; 
    $estudiantes = $_POST['estudiantes'];
    $n1ev = $_POST['notas_1ev'];
    $n1f = $_POST['notas_1final'];
    $n2ev = $_POST['notas_2ev'];
    $n2f = $_POST['notas_2final'];
    $obs = $_POST['observaciones'];

    $error = false;
    $total = count($estudiantes);

    for ($i = 0; $i < $total; $i++) {
        $idEst = $estudiantes[$i];
        $nota1 = $n1ev[$i];
        $nota2 = $n1f[$i];
        $nota3 = $n2ev[$i];
        $nota4 = $n2f[$i];
        $comentario = $obs[$i];

        // Mirar si son numeros
        if (!empty($nota1) && !is_numeric($nota1)) { $error = true; }
        if (!empty($nota2) && !is_numeric($nota2)) { $error = true; }
        if (!empty($nota3) && !is_numeric($nota3)) { $error = true; }
        if (!empty($nota4) && !is_numeric($nota4)) { $error = true; }

        if ($error == false) {
            // Entre 0 y 10
            if (is_numeric($nota1) && ($nota1 < 0 || $nota1 > 10)) { $error = true; }
            if (is_numeric($nota2) && ($nota2 < 0 || $nota2 > 10)) { $error = true; }
            if (is_numeric($nota3) && ($nota3 < 0 || $nota3 > 10)) { $error = true; }
            if (is_numeric($nota4) && ($nota4 < 0 || $nota4 > 10)) { $error = true; }
        }
if (!$error) {
...
if (!$error) {
    if (!$res) {
        $error = true;
    }
}
}

if (!$error) {
        require_once "../../comunes/notificaciones_grades.php";
        if (isset($_POST['notificarEstudiantes']) && !empty($_POST['notificarEstudiantes'])) {
            for ($j = 0; $j < $total; $j++) {
                $idEnvio = $estudiantes[$j];
                enviarEmailNotasEstudiante($idEnvio);
            }
        }
        $_SESSION['exito'] = "Listo! Ya se han guardado todas las notas.";
    } else {
        $_SESSION['error'] = "Vaya, parece que hay algun error en las notas. Revisa que sean numeros entre 0 y 10.";
    }

    header("Location: /pfc/vistas/profesores/calificaciones/agregar.php?idCiclo=$idCiclo&idModulo=$idMod");
    exit;
}

header("Location: /pfc/vistas/profesores/calificaciones/agregar.php");
exit;
?>

