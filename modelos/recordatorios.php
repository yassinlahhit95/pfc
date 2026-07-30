<?php
require_once __DIR__ . "/conectar.php";

function obtenerRecordatorios(int $idEvento): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM recordatorios WHERE idEvento = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $recordatorios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $recordatorios[] = $row;
    }
    return $recordatorios;
}

function crearRecordatoriosDefecto(int $idEvento): void {
    // Crear recordatorios por defecto si es necesario
    // Actualmente sin implementación - se puede extender según necesidad
}

function sincronizarRecordatorios(int $idEvento, array $tipos): void {
    $con = obtenerConexion();
    // Sincronizar tipos de recordatorios activos para un evento
    // Actualmente sin implementación completa
    // TODO: Implementar según lógica de negocio
}
