<?php
declare(strict_types=1);

// POST /api/v1/profile.php — self-service profile edit, all 5 roles.
// Body: any subset of the fields allowed for the caller's role (see
// PROFILE_EDITABLE_FIELDS below). Anything else in the body is silently
// ignored — this is a strict allow-list, not a generic column updater, so
// there is no way to reach idCiclo/dniEstudiante/email/role/etc. from here.
// secretaria has no self-editable contact fields in the current schema
// (only nombre/email/password) — always returns validation for that role.

require_once __DIR__ . '/_api.php';

const PROFILE_EDITABLE_FIELDS = [
    'estudiante' => ['telefonoEstudiante', 'direccionEstudiante', 'ciudadEstudiante', 'codigoPostalEstudiante'],
    'profesor'   => ['telefonoProfesor', 'direccionProfesor', 'ciudadProfesor', 'codigoPostalProfesor'],
    'director'   => ['telefonoDirector', 'direccionDirector', 'ciudadDirector', 'codigoPostalDirector'],
    'tutor'      => ['telefonoTutor'],
    'secretaria' => [],
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

$allowed = PROFILE_EDITABLE_FIELDS[$type] ?? [];
if (!$allowed) {
    v1Error('This role has no editable profile fields.', 400, 'validation');
}

$body = v1Body();
$toUpdate = [];
foreach ($allowed as $field) {
    if (array_key_exists($field, $body)) {
        $value = trim((string)$body[$field]);
        if (mb_strlen($value) > 255) {
            v1Error("$field must be 255 characters or fewer.", 400, 'validation');
        }
        $toUpdate[$field] = $value;
    }
}
if (!$toUpdate) {
    v1Error('No editable fields were provided.', 400, 'validation');
}

[$tabla, $idCol] = V1_USER_MAP[$type];
$con = obtenerConexion();

$setClauses = [];
$params = [];
$types = '';
foreach ($toUpdate as $field => $value) {
    $setClauses[] = "`$field` = ?"; // $field only ever comes from the hardcoded whitelist above
    $params[] = $value;
    $types .= 's';
}
$params[] = $uid;
$types .= 'i';

$sql = "UPDATE `$tabla` SET " . implode(', ', $setClauses) . " WHERE `$idCol` = ?";
$st = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($st, $types, ...$params);
if (!mysqli_stmt_execute($st)) {
    v1Error('Could not update the profile.', 500, 'error');
}

v1Ok(['message' => 'Profile updated.', 'updated' => array_keys($toUpdate)]);
