<?php
declare(strict_types=1);

// CRUD para Familiares (Tutores) de un Estudiante
require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/tutores.php';
require_once __DIR__ . '/../../modelos/log.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if (!in_array($type, ['director', 'secretaria'])) {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];
$con = obtenerConexion();

if ($method === 'GET') {
    $idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
    if (!$idEstudiante) v1Error('idEstudiante is required.', 400, 'validation');

    $sql = "SELECT t.idTutor, t.nombreTutor, t.emailTutor, t.dniTutor, t.telefonoTutor, et.parentesco
            FROM tutores t
            JOIN estudiante_tutor et ON t.idTutor = et.idTutor
            WHERE et.idEstudiante = ? ORDER BY t.nombreTutor ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $tutores = [];
    while ($row = mysqli_fetch_assoc($res)) {
        if (function_exists('_descifrarFilaTutor')) {
            $row = _descifrarFilaTutor($row);
        } else if (class_exists('Crypto')) {
            $row['nombreTutor'] = Crypto::decrypt($row['nombreTutor']) ?: $row['nombreTutor'];
            $row['emailTutor'] = Crypto::decrypt($row['emailTutor']) ?: $row['emailTutor'];
            $row['telefonoTutor'] = Crypto::decrypt($row['telefonoTutor']) ?: $row['telefonoTutor'];
            $row['dniTutor'] = Crypto::decryptDeterministic($row['dniTutor']) ?: $row['dniTutor'];
        }
        $tutores[] = [
            'idTutor' => (int)$row['idTutor'],
            'nombreTutor' => $row['nombreTutor'],
            'emailTutor' => $row['emailTutor'],
            'dniTutor' => $row['dniTutor'],
            'telefonoTutor' => $row['telefonoTutor'],
            'parentesco' => $row['parentesco'] ?? 'Tutor',
        ];
    }
    v1Ok(['tutores' => $tutores]);
}

if ($method === 'POST') {
    $body = v1Body();
    $idEstudiante = (int)($body['idEstudiante'] ?? 0);
    $nombre = trim((string)($body['nombreTutor'] ?? ''));
    $email = trim((string)($body['emailTutor'] ?? ''));
    $dni = trim((string)($body['dniTutor'] ?? ''));
    $telefono = trim((string)($body['telefonoTutor'] ?? ''));
    $parentesco = trim((string)($body['parentesco'] ?? 'Tutor'));

    if (!$idEstudiante || empty($nombre) || empty($email) || empty($dni)) {
        v1Error('idEstudiante, nombre, email, and dni are required.', 400, 'validation');
    }

    $tutorExistente = obtenerTutorPorDni($dni);
    $idTutor = 0;
    if ($tutorExistente) {
        $idTutor = (int)$tutorExistente['idTutor'];
    } else {
        $idTutor = insertarTutor($nombre, $email, $dni, $telefono);
        if (!$idTutor) {
            v1Error('Could not create tutor.', 500, 'error');
        }
    }

    if (!vincularEstudianteTutor($idEstudiante, $idTutor, $parentesco)) {
        v1Error('Could not link tutor to student.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('vincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor ($nombre)");
    } else {
        registrarAccionSecretaria('vincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor ($nombre)");
    }
    v1Ok(['success' => true, 'idTutor' => $idTutor]);
}

if ($method === 'PUT') {
    $body = v1Body();
    $idTutor = (int)($body['idTutor'] ?? 0);
    $idEstudiante = (int)($body['idEstudiante'] ?? 0);
    $nombre = trim((string)($body['nombreTutor'] ?? ''));
    $email = trim((string)($body['emailTutor'] ?? ''));
    $dni = trim((string)($body['dniTutor'] ?? ''));
    $telefono = trim((string)($body['telefonoTutor'] ?? ''));
    $parentesco = trim((string)($body['parentesco'] ?? ''));

    if (!$idTutor || !$idEstudiante || empty($nombre) || empty($email) || empty($dni)) {
        v1Error('idTutor, idEstudiante, nombre, email, and dni are required.', 400, 'validation');
    }

    if (!actualizarTutor($idTutor, $nombre, $email, $dni, $telefono)) {
        v1Error('Could not update tutor.', 500, 'error');
    }
    
    if (!empty($parentesco)) {
        $upd = mysqli_prepare($con, "UPDATE estudiante_tutor SET parentesco = ? WHERE idEstudiante = ? AND idTutor = ?");
        mysqli_stmt_bind_param($upd, "sii", $parentesco, $idEstudiante, $idTutor);
        mysqli_stmt_execute($upd);
    }

    if ($type === 'director') {
        registrarAccion('actualizar', 'tutores', $idTutor, $nombre);
    } else {
        registrarAccionSecretaria('actualizar', 'tutores', $idTutor, $nombre);
    }
    v1Ok(['success' => true]);
}

if ($method === 'DELETE') {
    $idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
    $idTutor = (int)($_GET['idTutor'] ?? 0);
    if (!$idEstudiante || !$idTutor) {
        v1Error('idEstudiante and idTutor are required.', 400, 'validation');
    }

    $del = mysqli_prepare($con, "DELETE FROM estudiante_tutor WHERE idEstudiante = ? AND idTutor = ?");
    mysqli_stmt_bind_param($del, "ii", $idEstudiante, $idTutor);
    if (!mysqli_stmt_execute($del)) {
        v1Error('Could not unlink tutor.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('desvincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor desvinculado");
    } else {
        registrarAccionSecretaria('desvincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor desvinculado");
    }
    v1Ok(['success' => true]);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
