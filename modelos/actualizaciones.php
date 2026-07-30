<?php
/**
 * Obtener actualizaciones recientes de directores, secretarias y profesores.
 *
 * Esta función devuelve un array de actualizaciones ordenado por fecha de
 * creación/actualización descendente. Cada elemento contiene la información
 * mínima necesaria para mostrar en el dashboard:
 *   - tipo: 'director' | 'secretaria' | 'profesor'
 *   - id   : identificador del registro
 *   - nombre: campo representativo del nombre o título
 *   - fecha: timestamp de la última modificación (se asume `fechaAlta` si no
 *            hay columna `updated_at`)
 *   - mensaje: texto corto para la UI
 */
function obtenerActualizacionesRecientes(int $limit = 5): array {
    $con = obtenerConexion();
    $queries = [];
    // Directores
    $queries[] = "SELECT 'director' AS tipo, idDirector AS id, nombreDirector AS nombre,
        fechaAltaDirector AS fecha,
        CONCAT('Nuevo director: ', nombreDirector) AS mensaje
        FROM directores WHERE fechaAltaDirector >= CURDATE()";
    // Secretarias
    $queries[] = "SELECT 'secretaria' AS tipo, idSecretaria AS id, nombreSecretaria AS nombre,
        fechaAltaSecretaria AS fecha,
        CONCAT('Nueva secretaria: ', nombreSecretaria) AS mensaje
        FROM secretarias WHERE fechaAltaSecretaria >= CURDATE()";
    // Profesores
    $queries[] = "SELECT 'profesor' AS tipo, idProfesor AS id, nombreProfesor AS nombre,
        fechaAltaProfesor AS fecha,
        CONCAT('Nuevo profesor: ', nombreProfesor) AS mensaje
        FROM profesores WHERE fechaAltaProfesor >= CURDATE()";

    $union = implode(' UNION ALL ', $queries);
    $sql = "SELECT * FROM ($union) AS u ORDER BY fecha DESC LIMIT ?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return [];
    mysqli_stmt_bind_param($stmt, 'i', $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $lista[] = $row;
    }
    return $lista;
}
