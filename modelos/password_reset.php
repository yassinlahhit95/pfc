<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

// Busca al usuario por email en todas las tablas de roles.
// Devuelve ['row' => [...], 'tipo' => 'admin'|'profesor'|'estudiante'|'tutor'] o null.
function buscarUsuarioPorEmail(string $email): ?array {
    $tablas = [
        'admin'      => ['directores',  'emailDirector',   'idDirector'],
        'profesor'   => ['profesores',  'emailProfesor',   'idProfesor'],
        'estudiante' => ['estudiantes', 'emailEstudiante', 'idEstudiante'],
        'tutor'      => ['tutores',     'emailTutor',      'idTutor'],
        'secretaria' => ['secretarias', 'emailSecretaria', 'idSecretaria'],
    ];
    foreach ($tablas as $tipo => [$tabla, $campoEmail, $campoId]) {
        $row = dbFetchOne("SELECT * FROM `$tabla` WHERE `$campoEmail` = ?", "s", $email);
        if ($row) {
            return ['row' => $row, 'tipo' => $tipo, 'campoId' => $campoId];
        }
    }
    return null;
}

function validarTokenReset(string $token): ?array {
    $row = dbFetchOne(
        "SELECT * FROM password_resets WHERE token = ? AND usado = 0 AND expires_at > NOW()",
        "s", hash('sha256', $token)
    );
    return $row ?: null;
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function crearTokenReset(string $email, string $tipo): string {
    $con   = obtenerConexion();
    $token = bin2hex(random_bytes(32));       // 256 bits — viaja en el enlace al usuario
    $tokenHash = hash('sha256', $token);      // solo el hash se persiste en BD
    $expira = date('Y-m-d H:i:s', time() + 3600);
    // Un token activo por cuenta — eliminar tokens anteriores no usados
    $del = mysqli_prepare($con, "DELETE FROM password_resets WHERE email = ? AND tipo_usuario = ?");
    mysqli_stmt_bind_param($del, "ss", $email, $tipo);
    mysqli_stmt_execute($del);
    $ins = mysqli_prepare($con, "INSERT INTO password_resets (token, email, tipo_usuario, expires_at) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($ins, "ssss", $tokenHash, $email, $tipo, $expira);
    mysqli_stmt_execute($ins);
    return $token;
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function marcarTokenUsado(string $token): void {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE password_resets SET usado = 1 WHERE token = ?");
    $hash = hash('sha256', $token);
    mysqli_stmt_bind_param($stmt, "s", $hash);
    mysqli_stmt_execute($stmt);
}

function cambiarPasswordPorEmail(string $email, string $tipo, string $nuevaPassword): bool {
    $con  = obtenerConexion();
    $hash = Security::hashPassword($nuevaPassword);
    $map  = [
        'admin'      => ['directores',  'password', 'emailDirector'],
        'profesor'   => ['profesores',  'password', 'emailProfesor'],
        'estudiante' => ['estudiantes', 'password', 'emailEstudiante'],
        'tutor'      => ['tutores',     'password', 'emailTutor'],
        'secretaria' => ['secretarias', 'password', 'emailSecretaria'],
    ];
    if (!isset($map[$tipo])) return false;
    [$tabla, $campoPass, $campoEmail] = $map[$tipo];
    // Limpia must_change_password: el usuario ya estableció su propia contraseña.
    $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET `$campoPass` = ?, `must_change_password` = 0 WHERE `$campoEmail` = ?");
    mysqli_stmt_bind_param($stmt, "ss", $hash, $email);
    $ok = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
    if ($ok && class_exists('Security')) {
        Security::touchPasswordChanged($con, $tabla, $campoEmail, $email);
    }
    return $ok;
}
