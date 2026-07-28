<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/eventos.php";

// ══════════════════════════════════════════════════════════════════════
// Log de entrega de recordatorios de eventos: una fila por (evento,
// destinatario, recordatorio) — quién debía recibir el aviso, cuándo se
// programó y si ya lo leyó. Vive en `notificaciones_recordatorios`, NO en
// `notificaciones` (esa es la campana genérica de la navbar — ver
// modelos/notificaciones.php — con un esquema totalmente distinto; usar el
// mismo nombre habría colisionado con esa tabla ya existente).
// ══════════════════════════════════════════════════════════════════════

// Roles válidos para tipoUsuario / audiencia (coincide con el enum de la tabla).
const NOTIF_ROLES_VALIDOS = ['director', 'profesor', 'secretaria', 'estudiante', 'tutor'];

// ══════════════════════════════════════════════════════════════════════
// AUDIENCIA
// ══════════════════════════════════════════════════════════════════════

// Calcula qué usuarios deben recibir notificación de un evento, según su
// tipo_visibilidad. Devuelve un array de ['id' => int, 'tipo' => string].
function obtenerAudienciaEvento(array $evento): array {
    $tipoVisibilidad = $evento['tipo_visibilidad'] ?? 'publica';
    $audienciaJson   = $evento['audiencia_json'] ?? null;

    // 'privada': Fase 2 — de momento solo se avisará al creador del evento,
    // lo cual se gestiona fuera de esta función. Aquí no hay audiencia.
    if ($tipoVisibilidad === 'privada') {
        return [];
    }

    // 'personalizado': la lista de destinatarios viene ya explícita en el
    // propio evento como [{"id":.., "tipo":".."}, ...] — no hace falta
    // volver a consultar las tablas de usuarios para esto.
    if ($tipoVisibilidad === 'personalizado') {
        $datos = json_decode($audienciaJson ?? '{}', true) ?: [];
        $lista = [];
        foreach (($datos['usuarios_custom'] ?? []) as $u) {
            if (!isset($u['id'], $u['tipo']) || !in_array($u['tipo'], NOTIF_ROLES_VALIDOS, true)) {
                continue;
            }
            $lista[] = ['id' => (int)$u['id'], 'tipo' => $u['tipo']];
        }
        return $lista;
    }

    $con = obtenerConexion();

    if ($tipoVisibilidad === 'roles') {
        // Solo los roles listados en audiencia_json.roles (JSON_CONTAINS sobre el array).
        $sql = "SELECT idDirector AS id, 'director' AS tipo FROM directores
                    WHERE JSON_CONTAINS(?, '\"director\"', '$.roles')
                UNION ALL
                SELECT idProfesor, 'profesor' FROM profesores
                    WHERE JSON_CONTAINS(?, '\"profesor\"', '$.roles')
                UNION ALL
                SELECT idSecretaria, 'secretaria' FROM secretarias
                    WHERE activoSecretaria = 1 AND JSON_CONTAINS(?, '\"secretaria\"', '$.roles')";
        $stmt = mysqli_prepare($con, $sql);
        if (!$stmt) {
            error_log("Error al preparar obtenerAudienciaEvento (roles): " . mysqli_error($con));
            return [];
        }
        $json = $audienciaJson ?: '{}';
        mysqli_stmt_bind_param($stmt, "sss", $json, $json, $json);
        mysqli_stmt_execute($stmt);
        $lista = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $lista;
    }

    // 'publica' (y cualquier valor no reconocido, por seguridad): todos los
    // directores + profesores + secretarias activas. Ni directores ni
    // profesores tienen baja lógica propia (solo secretarias, vía activoSecretaria).
    $sql = "SELECT idDirector AS id, 'director' AS tipo FROM directores
            UNION ALL
            SELECT idProfesor, 'profesor' FROM profesores
            UNION ALL
            SELECT idSecretaria, 'secretaria' FROM secretarias WHERE activoSecretaria = 1";
    $resultado = mysqli_query($con, $sql);
    if (!$resultado) {
        error_log("Error al consultar obtenerAudienciaEvento (publica): " . mysqli_error($con));
        return [];
    }
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

// ══════════════════════════════════════════════════════════════════════
// CREACIÓN DE NOTIFICACIONES
// ══════════════════════════════════════════════════════════════════════

// Crea una notificación por cada miembro de la audiencia del evento para un
// recordatorio concreto. Devuelve el número de filas creadas, o false si el
// evento no existe / la consulta falla.
function crearNotificacionesParaEvento(int $idEvento, int $idRecordatorio) {
    $idEvento       = (int)$idEvento;
    $idRecordatorio = (int)$idRecordatorio;

    $evento = obtenerEventoPorId($idEvento);
    if (!$evento) {
        error_log("crearNotificacionesParaEvento: evento no encontrado (idEvento={$idEvento})");
        return false;
    }

    $audiencia = obtenerAudienciaEvento($evento);
    if (empty($audiencia)) {
        return 0;
    }

    $con = obtenerConexion();
    $sql = "INSERT INTO notificaciones_recordatorios
                (idEvento, idUsuario, tipoUsuario, idRecordatorio, fecha_programada, estado)
            VALUES (?, ?, ?, ?, NOW(), 'pendiente')";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar crearNotificacionesParaEvento: " . mysqli_error($con));
        return false;
    }

    $creadas = 0;
    foreach ($audiencia as $destinatario) {
        $idUsuario   = (int)$destinatario['id'];
        $tipoUsuario = $destinatario['tipo'];
        mysqli_stmt_bind_param($stmt, "iisi", $idEvento, $idUsuario, $tipoUsuario, $idRecordatorio);
        if (mysqli_stmt_execute($stmt)) {
            $creadas++;
        } else {
            error_log("Error al crear notificación (idEvento={$idEvento}, idUsuario={$idUsuario}): " . mysqli_stmt_error($stmt));
        }
    }
    mysqli_stmt_close($stmt);
    return $creadas;
}

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS PARA EL USUARIO
// ══════════════════════════════════════════════════════════════════════

// Recordatorios pendientes de leer de un usuario, para el widget del dashboard.
function obtenerNotificacionesNoLeidas(int $idUsuario, string $tipoUsuario, int $limite = 10): array {
    $idUsuario = (int)$idUsuario;
    $limite    = (int)$limite;
    $con = obtenerConexion();
    $sql = "SELECT nr.idNotificacionRecordatorio AS idNotificacion, nr.idEvento,
                   e.tituloEvento, e.fechaEvento, e.horaEvento,
                   nr.fecha_programada, nr.fecha_enviada, nr.estado
            FROM notificaciones_recordatorios nr
            INNER JOIN eventos e ON e.idEvento = nr.idEvento
            WHERE nr.idUsuario = ? AND nr.tipoUsuario = ? AND nr.leido = 0
            ORDER BY nr.fecha_programada DESC
            LIMIT ?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar obtenerNotificacionesNoLeidas: " . mysqli_error($con));
        return [];
    }
    mysqli_stmt_bind_param($stmt, "isi", $idUsuario, $tipoUsuario, $limite);
    mysqli_stmt_execute($stmt);
    $lista = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $lista;
}

// Marca una notificación de recordatorio como leída.
function marcarComoLeida(int $idNotificacion): bool {
    $idNotificacion = (int)$idNotificacion;
    $con = obtenerConexion();
    $sql = "UPDATE notificaciones_recordatorios SET leido = 1 WHERE idNotificacionRecordatorio = ?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar marcarComoLeida: " . mysqli_error($con));
        return false;
    }
    mysqli_stmt_bind_param($stmt, "i", $idNotificacion);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log("Error al ejecutar marcarComoLeida (idNotificacion={$idNotificacion}): " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
    return $ok;
}

// ══════════════════════════════════════════════════════════════════════
// CRON: PROCESAR RECORDATORIOS PENDIENTES
// ══════════════════════════════════════════════════════════════════════

// Busca recordatorios cuyo momento de disparo ya llegó (fechaEvento/horaEvento
// menos minutos_antes <= NOW()) y que todavía no tienen notificaciones creadas,
// y genera las notificaciones de su audiencia. Limitado a 100 por llamada
// (pensado para invocarse periódicamente desde un cron).
function procesarRecordatoriosPendientes(): array {
    $con = obtenerConexion();
    $sql = "SELECT r.idRecordatorio, r.idEvento
            FROM recordatorios r
            INNER JOIN eventos e ON e.idEvento = r.idEvento
            WHERE r.activo = 1
              AND e.activo = 1
              AND TIMESTAMPADD(MINUTE, -r.minutos_antes, TIMESTAMP(e.fechaEvento, COALESCE(e.horaEvento, '00:00:00'))) <= NOW()
              AND NOT EXISTS (
                  SELECT 1 FROM notificaciones_recordatorios nr WHERE nr.idRecordatorio = r.idRecordatorio
              )
            LIMIT 100";
    $resultado = mysqli_query($con, $sql);
    if (!$resultado) {
        error_log("Error al consultar procesarRecordatoriosPendientes: " . mysqli_error($con));
        return ['procesados' => 0, 'creados' => 0];
    }

    $procesados = 0;
    $creados    = 0;
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $procesados++;
        $n = crearNotificacionesParaEvento((int)$fila['idEvento'], (int)$fila['idRecordatorio']);
        if ($n !== false) {
            $creados += $n;
        }
    }
    return ['procesados' => $procesados, 'creados' => $creados];
}
