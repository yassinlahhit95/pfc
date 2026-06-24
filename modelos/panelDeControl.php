<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONTADORES PARA EL PANEL DE CONTROL
// ══════════════════════════════════════════════════════════════════════

function contarEstudiantes(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM estudiantes")['total'] ?? 0);
}

function contarProfesores(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM profesores")['total'] ?? 0);
}

function contarSecretarias(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM secretarias")['total'] ?? 0);
}



function contarCiclos(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM ciclos")['total'] ?? 0);
}

function contarEstudiantesDeProfesor(int $idProfesor): int {
    $row = dbFetchOne(
        "SELECT COUNT(DISTINCT e.idEstudiante) as total FROM estudiantes e
         WHERE e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
            OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)",
        "ii", $idProfesor, $idProfesor
    );
    return (int)($row['total'] ?? 0);
}

function contarCiclosDeProfesor(int $idProfesor): int {
    $row = dbFetchOne(
        "SELECT COUNT(DISTINCT c.idCiclo) as total FROM ciclos c
         WHERE c.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
            OR c.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)",
        "ii", $idProfesor, $idProfesor
    );
    return (int)($row['total'] ?? 0);
}

function contarModulos(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM modulos")['total'] ?? 0);
}

function contarRetos(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM retos")['total'] ?? 0);
}



function obtenerTotalRecaudado(): float {
    return (float)(dbFetchOne("SELECT SUM(monto) as acumulado FROM pagos")['acumulado'] ?? 0);
}

function contarPagosRealizados(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM pagos")['total'] ?? 0);
}

function contarTFGsEntregados(): int {
    return (int)(dbFetchOne(
        "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG != '' AND archivoTFG IS NOT NULL"
    )['total'] ?? 0);
}

// Obtiene todos los contadores del nav de admin en una sola consulta.
function obtenerContadoresNavAdmin(int $idAdmin = 0): array {
    $con = obtenerConexion();
    $idAdmin = (int)$idAdmin;
    $res = mysqli_query($con,
        "SELECT
            (SELECT COUNT(*) FROM estudiantes)                                      AS total_estudiantes,
            (SELECT COUNT(*) FROM profesores)                                       AS total_profesores,
            (SELECT COUNT(*) FROM tutores)                                          AS total_tutores,
            (SELECT COUNT(*) FROM directores)                                       AS total_directores,
            (SELECT COUNT(*) FROM ciclos)                                           AS total_ciclos,
            (SELECT COUNT(*) FROM modulos)                                          AS total_modulos,
            (SELECT COUNT(*) FROM retos)                                            AS total_retos,
            (SELECT COUNT(*) FROM anuncios)                                         AS total_anuncios,
            (SELECT COUNT(*) FROM dispositivos)                                     AS total_inventario,
            (SELECT COUNT(*) FROM prestamos WHERE estadoPrestamo = 'en curso')      AS total_prestamos,
            (SELECT COUNT(*) FROM pagos)                                            AS total_pagos,
            (SELECT COUNT(*) FROM reclamaciones
             WHERE (emisor_rol = 'estudiante' AND idProfesor IS NULL)
                OR (emisor_rol = 'profesor'   AND idEstudiante IS NULL)
                OR (emisor_rol = 'admin'))                                          AS total_mensajes,
            (SELECT COUNT(*) FROM reclamaciones
             WHERE leido = 0
               AND ((emisor_rol = 'estudiante' AND idProfesor IS NULL)
                 OR (emisor_rol = 'profesor'   AND idEstudiante IS NULL)))          AS total_sin_leer,
            (SELECT COUNT(*) FROM pre_matriculas WHERE estado = 'PENDIENTE')        AS total_admisiones_pendientes,
            (SELECT COUNT(*) FROM chat_mensajes m
             JOIN chat_conversaciones c ON m.conversacion_id = c.id
             WHERE m.leido = 0
               AND NOT (m.emisor_rol = 'admin' AND m.emisor_id = {$idAdmin})
               AND (  (c.user_a_rol = 'admin' AND c.user_a_id = {$idAdmin})
                   OR (c.user_b_rol = 'admin' AND c.user_b_id = {$idAdmin})))      AS total_chat_no_leidos"
    );
    $row = $res ? mysqli_fetch_assoc($res) : [];
    return array_map('intval', $row ?: []);
}
