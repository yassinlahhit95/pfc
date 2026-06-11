<?php
require_once __DIR__ . "/conectar.php";

/**
 * Find user row + role for the given email.
 * Returns ['row' => [...], 'tipo' => 'admin'|'profesor'|'estudiante'] or null.
 */
function buscarUsuarioPorEmail(string $email): ?array {
    $tablas = [
        'admin'       => ['directores',  'emailDirector',   'idDirector'],
        'profesor'    => ['profesores',   'emailProfesor',   'idProfesor'],
        'estudiante'  => ['estudiantes',  'emailEstudiante', 'idEstudiante'],
    ];
    foreach ($tablas as $tipo => [$tabla, $campoEmail, $campoId]) {
        $row = dbFetchOne("SELECT * FROM `$tabla` WHERE `$campoEmail` = ?", "s", $email);
        if ($row) {
            return ['row' => $row, 'tipo' => $tipo, 'campoId' => $campoId];
        }
    }
    return null;
}

function crearTokenReset(string $email, string $tipo): string {
    $con   = obtenerConexion();
    $token = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', time() + 3600);

    // Remove any existing unused token for this email+type
    $del = mysqli_prepare($con, "DELETE FROM password_resets WHERE email = ? AND tipo_usuario = ?");
    mysqli_stmt_bind_param($del, "ss", $email, $tipo);
    mysqli_stmt_execute($del);

    $ins = mysqli_prepare($con, "INSERT INTO password_resets (token, email, tipo_usuario, expires_at) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($ins, "ssss", $token, $email, $tipo, $expira);
    mysqli_stmt_execute($ins);

    return $token;
}

function validarTokenReset(string $token): ?array {
    $row = dbFetchOne(
        "SELECT * FROM password_resets WHERE token = ? AND usado = 0 AND expires_at > NOW()",
        "s", $token
    );
    return $row ?: null;
}

function marcarTokenUsado(string $token): void {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE password_resets SET usado = 1 WHERE token = ?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
}

function cambiarPasswordPorEmail(string $email, string $tipo, string $nuevaPassword): bool {
    $con = obtenerConexion();
    $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT, ['cost' => 12]);

    $map = [
        'admin'      => ['directores', 'password', 'emailDirector'],
        'profesor'   => ['profesores',  'password', 'emailProfesor'],
        'estudiante' => ['estudiantes', 'password', 'emailEstudiante'],
    ];
    [$tabla, $campoPass, $campoEmail] = $map[$tipo];

    $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET `$campoPass` = ? WHERE `$campoEmail` = ?");
    mysqli_stmt_bind_param($stmt, "ss", $hash, $email);
    return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
}
