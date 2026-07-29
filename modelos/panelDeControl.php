<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/../include/Cache.php";

// ══════════════════════════════════════════════════════════════════════
// CONTADORES PARA EL PANEL DE CONTROL
// ══════════════════════════════════════════════════════════════════════

// Contadores de portada del dashboard — leídos en cada visita a
// inicio/dashboard.php, escritos rarísima vez (dar de alta/baja un
// estudiante, etc.): candidatos ideales para el mismo patrón de caché
// de 60s que ya usa obtenerContadoresNavAdmin() más abajo.
function contarEstudiantes(): int {
    return Cache::remember('panel_total_estudiantes', 60, function () {
        return (int)(dbFetchOne("SELECT COUNT(*) as total FROM estudiantes WHERE deleted_at IS NULL")['total'] ?? 0);
    });
}

function contarProfesores(): int {
    return Cache::remember('panel_total_profesores', 60, function () {
        return (int)(dbFetchOne("SELECT COUNT(*) as total FROM profesores")['total'] ?? 0);
    });
}

function contarSecretarias(): int {
    return Cache::remember('panel_total_secretarias', 60, function () {
        return (int)(dbFetchOne("SELECT COUNT(*) as total FROM secretarias")['total'] ?? 0);
    });
}



function contarCiclos(): int {
    return Cache::remember('panel_total_ciclos', 60, function () {
        return (int)(dbFetchOne("SELECT COUNT(*) as total FROM ciclos")['total'] ?? 0);
    });
}

function contarEstudiantesDeProfesor(int $idProfesor): int {
    return Cache::remember("panel_estudiantes_profesor_{$idProfesor}", 60, function () use ($idProfesor) {
        // Los paréntesis son necesarios: sin ellos, "AND ... OR ..." aplicaría el filtro
        // eliminado=0 solo a la primera mitad y contaría estudiantes dados de baja
        // llegados por la vía "profesor de módulo".
        $row = dbFetchOne(
            "SELECT COUNT(DISTINCT e.idEstudiante) as total FROM estudiantes e
             WHERE e.deleted_at IS NULL AND (e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
                OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?))",
            "ii", $idProfesor, $idProfesor
        );
        return (int)($row['total'] ?? 0);
    });
}

function contarCiclosDeProfesor(int $idProfesor): int {
    return Cache::remember("panel_ciclos_profesor_{$idProfesor}", 60, function () use ($idProfesor) {
        $row = dbFetchOne(
            "SELECT COUNT(DISTINCT c.idCiclo) as total FROM ciclos c
             WHERE c.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
                OR c.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)",
            "ii", $idProfesor, $idProfesor
        );
        return (int)($row['total'] ?? 0);
    });
}

function contarModulos(): int {
    return Cache::remember('panel_total_modulos', 60, function () {
        return (int)(dbFetchOne("SELECT COUNT(*) as total FROM modulos")['total'] ?? 0);
    });
}

function contarRetos(): int {
    return Cache::remember('panel_total_retos', 60, function () {
        return (int)(dbFetchOne("SELECT COUNT(*) as total FROM retos")['total'] ?? 0);
    });
}



function obtenerTotalRecaudado(): float {
    return Cache::remember('panel_total_recaudado', 60, function () {
        return (float)(dbFetchOne("SELECT SUM(monto) as acumulado FROM pagos")['acumulado'] ?? 0);
    });
}

function contarEstudiantesNuevos(int $dias = 7): int {
    return Cache::remember("panel_estudiantes_nuevos_{$dias}", 60, function () use ($dias) {
        $row = dbFetchOne(
            "SELECT COUNT(*) as total FROM estudiantes WHERE deleted_at IS NULL AND fechaAltaEstudiante >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            "i", $dias
        );
        return (int)($row['total'] ?? 0);
    });
}

function contarProfesoresNuevos(int $dias = 7): int {
    return Cache::remember("panel_profesores_nuevos_{$dias}", 60, function () use ($dias) {
        $row = dbFetchOne(
            "SELECT COUNT(*) as total FROM profesores WHERE fechaAltaProfesor >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            "i", $dias
        );
        return (int)($row['total'] ?? 0);
    });
}

function contarTFGsEntregados(): int {
    return Cache::remember('panel_tfgs_entregados', 60, function () {
        return (int)(dbFetchOne(
            "SELECT COUNT(*) as total FROM estudiantes WHERE deleted_at IS NULL AND archivoTFG != '' AND archivoTFG IS NOT NULL"
        )['total'] ?? 0);
    });
}

// Obtiene todos los contadores del nav de admin. Los contadores globales
// (no dependen del admin concreto) se cachean en APCu y se comparten entre
// todas las sesiones admin/secretaria concurrentes en vez de recalcularse
// por sesión; solo el contador de chat, que sí depende de $idAdmin, se
// cachea por admin.
function obtenerContadoresNavAdmin(int $idAdmin = 0): array {
    $idAdmin = (int)$idAdmin;

    $globalQueries = [
        'total_estudiantes' => "SELECT COUNT(*) FROM estudiantes WHERE deleted_at IS NULL",
        'total_profesores' => "SELECT COUNT(*) FROM profesores",
        'total_tutores' => "SELECT COUNT(*) FROM tutores",
        'total_directores' => "SELECT COUNT(*) FROM directores",
        'total_ciclos' => "SELECT COUNT(*) FROM ciclos",
        'total_modulos' => "SELECT COUNT(*) FROM modulos",
        'total_retos' => "SELECT COUNT(*) FROM retos",
        'total_anuncios' => "SELECT COUNT(*) FROM anuncios",
        'total_inventario' => "SELECT COUNT(*) FROM dispositivos",
        'total_prestamos' => "SELECT COUNT(*) FROM prestamos WHERE estadoPrestamo = 'en curso'",
        'total_pagos' => "SELECT COUNT(*) FROM pagos",
        'total_mensajes' => "SELECT COUNT(*) FROM reclamaciones WHERE (emisor_rol = 'estudiante' AND idProfesor IS NULL) OR (emisor_rol = 'profesor' AND idEstudiante IS NULL) OR (emisor_rol = 'admin')",
        'total_sin_leer' => "SELECT COUNT(*) FROM reclamaciones WHERE leido = 0 AND ((emisor_rol = 'estudiante' AND idProfesor IS NULL) OR (emisor_rol = 'profesor' AND idEstudiante IS NULL))",
        'total_admisiones_pendientes' => "SELECT COUNT(*) FROM pre_matriculas WHERE estado IN ('pendiente', 'revisando')",
    ];

    $data = Cache::remember('nav_admin_counts_global', 60, function () use ($globalQueries) {
        $con = obtenerConexion();
        $out = [];
        foreach ($globalQueries as $key => $sql) {
            $res = mysqli_query($con, $sql);
            $row = $res ? mysqli_fetch_row($res) : [0];
            $out[$key] = (int)($row[0] ?? 0);
        }
        return $out;
    });

    $data['total_chat_no_leidos'] = Cache::remember("nav_admin_chat_no_leidos_{$idAdmin}", 60, function () use ($idAdmin) {
        $con = obtenerConexion();
        $stmt = mysqli_prepare($con,
            "SELECT COUNT(*) FROM chat_mensajes m JOIN chat_conversaciones c ON m.conversacion_id = c.id
             WHERE m.leido = 0 AND NOT (m.emisor_rol = 'admin' AND m.emisor_id = ?)
             AND ((c.user_a_rol = 'admin' AND c.user_a_id = ?) OR (c.user_b_rol = 'admin' AND c.user_b_id = ?))");
        mysqli_stmt_bind_param($stmt, "iii", $idAdmin, $idAdmin, $idAdmin);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_row(mysqli_stmt_get_result($stmt));
        return (int)($row[0] ?? 0);
    });

    return $data;
}
