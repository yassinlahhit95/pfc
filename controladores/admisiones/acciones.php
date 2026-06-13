<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../../modelos/admisiones.php";
require_once __DIR__ . "/../../modelos/ciclos.php";

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'check_dni':
        $dni = $_POST['dni'] ?? '';
        if (empty($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'DNI no proporcionado']);
            break;
        }
        $registro = obtenerPreMatriculaPorDni($dni);
        if ($registro) {
            echo json_encode(['status' => 'exists', 'data' => $registro]);
        } else {
            echo json_encode(['status' => 'new']);
        }
        break;

    case 'consultar_estado':
        $dni = $_POST['dni'] ?? '';
        if (empty($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'DNI no proporcionado']);
            break;
        }
        $registro = obtenerPreMatriculaPorDni($dni);
        if ($registro) {
            echo json_encode([
                'status' => 'success', 
                'data' => [
                    'nombre' => $registro['nombre'],
                    'estado' => $registro['estado'],
                    'ciclo' => $registro['nombreCiclo'],
                    'observaciones' => $registro['observaciones'],
                    'fecha' => date('d/m/Y', strtotime($registro['fechaSolicitud']))
                ]
            ]);
        } else {
            echo json_encode(['status' => 'not_found', 'message' => 'No se ha encontrado ninguna solicitud con ese DNI']);
        }
        break;

    case 'step1':
        $dni = $_POST['dni'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $idCiclo = $_POST['idCiclo'] ?? '';
        $curso = $_POST['curso'] ?? '1º';

        if (empty($dni) || empty($nombre) || empty($apellidos) || empty($email) || empty($idCiclo)) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios']);
            break;
        }

        // VALIDACIÓN DE DUPLICADOS
        if (obtenerPreMatriculaPorDni($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe una solicitud registrada con este DNI.']);
            break;
        }

        if (obtenerPreMatriculaPorEmail($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe una solicitud registrada con este correo electrónico.']);
            break;
        }

        $id = crearPreMatricula($dni, $nombre, $apellidos, $email, $telefono, $idCiclo, $curso);
        if ($id) {
            echo json_encode(['status' => 'success', 'idPreMatricula' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar los datos']);
        }
        break;

    case 'upload':
        $idPreMatricula = $_POST['idPreMatricula'] ?? '';
        $tipo = $_POST['tipoDocumento'] ?? ''; // DNI_FRONTAL, DNI_REVERSO, EXPEDIENTE

        if (empty($idPreMatricula) || empty($_FILES['archivo'])) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos para la subida']);
            break;
        }

        $file = $_FILES['archivo'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = "adm_" . $idPreMatricula . "_" . $tipo . "_" . time() . "." . $ext;
        $dest = "../../public/uploads/admisiones/" . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            registrarArchivoPreMatricula($idPreMatricula, $tipo, $file['name'], "/public/uploads/admisiones/" . $newName);
            echo json_encode(['status' => 'success', 'filename' => $file['name']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo']);
        }
        break;

    case 'finalize':
        $idPreMatricula = $_POST['idPreMatricula'] ?? '';
        if (empty($idPreMatricula)) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            break;
        }
        actualizarEstadoPreMatricula($idPreMatricula, 'EN_REVISION');
        echo json_encode(['status' => 'success']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}
?>
