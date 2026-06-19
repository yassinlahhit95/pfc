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

function contarDirectores(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM directores")['total'] ?? 0);
}

function contarAnuncios(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM anuncios")['total'] ?? 0);
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

function contarInventario(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM dispositivos")['total'] ?? 0);
}

function contarPrestamosActivos(): int {
    return (int)(dbFetchOne("SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'")['total'] ?? 0);
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
