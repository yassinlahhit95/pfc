<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/recordatorios.php";
require_once __DIR__ . "/notificacionesRecordatorios.php"; // notificaciones_recordatorios, no confundir con notificaciones.php (campana navbar)

// Valores válidos del enum tipo_visibilidad de la tabla eventos.
const EVENTOS_VISIBILIDAD_VALIDAS = ['publica', 'roles', 'personalizado', 'privada'];

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

// Todos los eventos (pasados y futuros) para la gestión de secretaría:
// así el buscador encuentra también eventos ya celebrados.
function listarTodosLosEventos() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM eventos ORDER BY fechaEvento DESC, horaEvento DESC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarEventosProximos() {
    $con = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT * FROM eventos WHERE fechaEvento >= ? ORDER BY fechaEvento ASC, horaEvento ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $hoy);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerEventoPorId($idEvento) {
    $idEvento = (int)$idEvento;
    $con = obtenerConexion();
    $sql = "SELECT * FROM eventos WHERE idEvento = ? AND activo = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $evento = mysqli_fetch_assoc($resultado) ?: false;
    if ($evento) {
        $evento['recordatorios'] = obtenerRecordatorios($idEvento);
    }
    return $evento;
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $sql = "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $titulo, $descripcion, $fecha, $hora, $ubicacion);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarEvento($idEvento, $titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $sql = "UPDATE eventos SET tituloEvento=?, descripcionEvento=?, fechaEvento=?, horaEvento=?, ubicacionEvento=? WHERE idEvento=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $titulo, $descripcion, $fecha, $hora, $ubicacion, $idEvento);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarEvento($idEvento) {
    $con = obtenerConexion();
    $sql = "DELETE FROM eventos WHERE idEvento = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// CRUD CON RECORDATORIOS (calendario + avisos)
// ══════════════════════════════════════════════════════════════════════

// Crea un evento y sincroniza sus recordatorios. $data admite:
// tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento,
// idCreador, tipo_visibilidad, audiencia_json (string JSON o array),
// recordatorios (array de tipos a activar, p.ej. ['24h_antes','1h_antes']).
// Devuelve el idEvento creado o false si falla validación o la inserción.
function crearEvento(array $data) {
    $titulo = trim($data['tituloEvento'] ?? '');
    $fecha  = trim($data['fechaEvento'] ?? '');
    if ($titulo === '' || $fecha === '') {
        error_log("crearEvento: tituloEvento y fechaEvento son obligatorios");
        return false;
    }
    $tipoVisibilidad = $data['tipo_visibilidad'] ?? 'publica';
    if (!in_array($tipoVisibilidad, EVENTOS_VISIBILIDAD_VALIDAS, true)) {
        error_log("crearEvento: tipo_visibilidad inválido ({$tipoVisibilidad})");
        return false;
    }

    $descripcion = $data['descripcionEvento'] ?? null;
    $hora        = $data['horaEvento'] ?? null;
    $ubicacion   = $data['ubicacionEvento'] ?? null;
    $idCreador   = (int)($data['idCreador'] ?? 0);

    $audiencia = $data['audiencia_json'] ?? null;
    if (is_array($audiencia)) {
        $audiencia = json_encode($audiencia);
    }

    $con = obtenerConexion();
    $sql = "INSERT INTO eventos
                (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento, idCreador, tipo_visibilidad, audiencia_json)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar crearEvento: " . mysqli_error($con));
        return false;
    }
    mysqli_stmt_bind_param(
        $stmt, "sssssiss",
        $titulo, $descripcion, $fecha, $hora, $ubicacion, $idCreador, $tipoVisibilidad, $audiencia
    );
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Error al ejecutar crearEvento: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    $idEvento = mysqli_insert_id($con);
    mysqli_stmt_close($stmt);

    crearRecordatoriosDefecto($idEvento);
    sincronizarRecordatorios($idEvento, $data['recordatorios'] ?? []);

    return $idEvento;
}

// Actualiza solo los campos presentes en $data (edición parcial) y, si viene
// 'recordatorios', sincroniza qué tipos quedan activos. Devuelve bool.
function editarEvento($idEvento, array $data): bool {
    $idEvento = (int)$idEvento;
    $con = obtenerConexion();

    $camposSimples = [
        'tituloEvento'      => 's',
        'descripcionEvento' => 's',
        'fechaEvento'       => 's',
        'horaEvento'        => 's',
        'ubicacionEvento'   => 's',
        'tipo_visibilidad'  => 's',
    ];

    $sets    = [];
    $tipos   = '';
    $valores = [];
    foreach ($camposSimples as $campo => $tipo) {
        if (array_key_exists($campo, $data)) {
            $sets[]    = "$campo = ?";
            $tipos    .= $tipo;
            $valores[] = $data[$campo];
        }
    }
    if (array_key_exists('audiencia_json', $data)) {
        $audiencia = $data['audiencia_json'];
        if (is_array($audiencia)) {
            $audiencia = json_encode($audiencia);
        }
        $sets[]    = "audiencia_json = ?";
        $tipos    .= 's';
        $valores[] = $audiencia;
    }

    $ok = true;
    if (!empty($sets)) {
        $sets[]    = "updated_at = NOW()";
        $sql       = "UPDATE eventos SET " . implode(', ', $sets) . " WHERE idEvento = ? AND activo = 1";
        $stmt      = mysqli_prepare($con, $sql);
        if (!$stmt) {
            error_log("Error al preparar editarEvento: " . mysqli_error($con));
            return false;
        }
        $tipos    .= 'i';
        $valores[] = $idEvento;
        mysqli_stmt_bind_param($stmt, $tipos, ...$valores);
        $ok = mysqli_stmt_execute($stmt);
        if (!$ok) {
            error_log("Error al ejecutar editarEvento (idEvento={$idEvento}): " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    }

    if ($ok && array_key_exists('recordatorios', $data)) {
        $ok = sincronizarRecordatorios($idEvento, $data['recordatorios']);
    }

    return $ok;
}

// Baja lógica de un evento (activo = 0). No toca sus recordatorios/notificaciones.
function borrarEventoSuave($idEvento): bool {
    $idEvento = (int)$idEvento;
    $con = obtenerConexion();
    $sql = "UPDATE eventos SET activo = 0, updated_at = NOW() WHERE idEvento = ?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar borrarEventoSuave: " . mysqli_error($con));
        return false;
    }
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log("Error al ejecutar borrarEventoSuave (idEvento={$idEvento}): " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
    return $ok;
}

// Eventos visibles para un usuario según tipo_visibilidad: públicos siempre,
// 'roles' si su rol está en audiencia_json.roles, 'personalizado' si su id
// está en audiencia_json.usuarios_custom. 'privada' queda fuera (solo la ve
// su creador, gestionado aparte). Filtro de rango de fechas opcional.
function obtenerEventosParaUsuario(int $idUsuario, string $tipoUsuario, ?string $fechaInicio = null, ?string $fechaFin = null): array {
    $idUsuario = (int)$idUsuario;
    $con = obtenerConexion();

    $rolJson = json_encode($tipoUsuario); // p.ej. "profesor" -> '"profesor"'

    $sql = "SELECT * FROM eventos
            WHERE activo = 1
              AND (
                    tipo_visibilidad = 'publica'
                 OR (tipo_visibilidad = 'roles' AND JSON_CONTAINS(audiencia_json, ?, '$.roles'))
                 OR (tipo_visibilidad = 'personalizado' AND JSON_CONTAINS(audiencia_json, ?, '$.usuarios_custom'))
              )";
    // idUsuario se envía como string: JSON_CONTAINS necesita un literal JSON
    // válido en su 2º argumento y el protocolo binario de mysqli con tipo 'i'
    // no lo tipa como tal (MySQL responde "Invalid data type for JSON data").
    $tipos   = 'ss';
    $valores = [$rolJson, (string)$idUsuario];

    if ($fechaInicio !== null) {
        $sql      .= " AND fechaEvento >= ?";
        $tipos    .= 's';
        $valores[] = $fechaInicio;
    }
    if ($fechaFin !== null) {
        $sql      .= " AND fechaEvento <= ?";
        $tipos    .= 's';
        $valores[] = $fechaFin;
    }
    $sql .= " ORDER BY fechaEvento DESC";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar obtenerEventosParaUsuario: " . mysqli_error($con));
        return [];
    }
    mysqli_stmt_bind_param($stmt, $tipos, ...$valores);
    mysqli_stmt_execute($stmt);
    $lista = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $lista;
}

// Todos los eventos para gestión de administración, con filtros opcionales:
// 'solo_activos' (bool) y 'idCreador' (int).
function listarTodosEventos(array $filtros = []): array {
    $con = obtenerConexion();
    $sql     = "SELECT * FROM eventos WHERE 1=1";
    $tipos   = '';
    $valores = [];

    if (!empty($filtros['solo_activos'])) {
        $sql .= " AND activo = 1";
    }
    if (!empty($filtros['idCreador'])) {
        $sql      .= " AND idCreador = ?";
        $tipos    .= 'i';
        $valores[] = (int)$filtros['idCreador'];
    }
    $sql .= " ORDER BY fechaEvento DESC";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar listarTodosEventos: " . mysqli_error($con));
        return [];
    }
    if ($valores) {
        mysqli_stmt_bind_param($stmt, $tipos, ...$valores);
    }
    mysqli_stmt_execute($stmt);
    $lista = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $lista;
}
