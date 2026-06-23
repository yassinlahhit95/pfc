<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../include/credenciales.php";
require_once __DIR__ . "/../../../modelos/log.php";

if (isset($_POST['guardarTutor'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/tutores/agregarTutor.php");
        exit;
    }
    $nombre    = trim($_POST['nombreTutor'] ?? '');
    $email     = trim($_POST['emailTutor'] ?? '');
    $dni       = trim($_POST['dniTutor'] ?? '');
    $telefono  = trim($_POST['telefonoTutor'] ?? '');
    $parentesco = trim($_POST['parentesco'] ?? 'Tutor Legal');
    $estudiantesVinculados = isset($_POST['estudiantes']) && is_array($_POST['estudiantes']) ? $_POST['estudiantes'] : [];

    $errores = [];
    if (empty($nombre)) $errores['nombreTutor'] = "El nombre es obligatorio.";
    if (empty($email)) {
        $errores['emailTutor'] = "El correo electrónico es obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $errores['emailTutor'] = "El formato del correo electrónico no es válido.";
    }
    if (empty($dni)) $errores['dniTutor'] = "El DNI/NIE es obligatorio.";

    if (empty($errores) && checkTutorExistente($dni, $email)) {
        $errores['dniTutor'] = "Ya existe un familiar registrado con ese DNI o correo electrónico.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_tutor'] = $_POST;
        header("Location: ../../../vistas/admin/tutores/agregarTutor.php");
        exit;
    }

    $idNuevo = insertarTutor($nombre, $email, $dni, $telefono);
    if ($idNuevo) {
        registrarAccion('insertar', 'tutores', $idNuevo, $nombre);
        foreach ($estudiantesVinculados as $idEst) {
            $idEst = (int)$idEst;
            if ($idEst > 0) vincularEstudianteTutor($idEst, $idNuevo, $parentesco);
        }
        $_SESSION['exito'] = mensajeExitoConCredenciales("El familiar ha sido registrado y vinculado correctamente.");
        header("Location: ../../../vistas/admin/tutores/verTutores.php");
        exit;
    }

    $_SESSION['errores'] = "Error al registrar el familiar. Inténtelo de nuevo.";
    $_SESSION['datos_tutor'] = $_POST;
    header("Location: ../../../vistas/admin/tutores/agregarTutor.php");
    exit;
}

header("Location: ../../../vistas/admin/tutores/verTutores.php");
exit;
