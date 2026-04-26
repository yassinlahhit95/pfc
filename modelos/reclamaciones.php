<?php
require_once("conectar.php");

/**
 * Lista todos los mensajes registrados en el sistema para la administración
 */
function listarTodosLosMensajes() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor 
                    FROM reclamaciones r
                    LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
                    LEFT JOIN profesores p ON r.idProfesor = p.idProfesor
                    ORDER BY r.idReclamacion DESC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaDeMensajesFinal = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaDeMensajesFinal[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaDeMensajesFinal;
}

/**
 * Obtiene un mensaje específico por su identificador
 */
function obtenerMensajePorId($idReclamacionRecibido) {
    $conexionBaseDatos = obtenerConexion();
    
    // Usamos sentencias preparadas para mayor seguridad
    $preparacion = mysqli_prepare($conexionBaseDatos, "SELECT r.*, e.nombreEstudiante, p.nombreProfesor 
                                                    FROM reclamaciones r
                                                    LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
                                                    LEFT JOIN profesores p ON r.idProfesor = p.idProfesor
                                                    WHERE r.idReclamacion = ?");
    
    mysqli_stmt_bind_param($preparacion, "i", $idReclamacionRecibido);
    mysqli_stmt_execute($preparacion);
    
    $resultadoFinal = mysqli_stmt_get_result($preparacion);
    $datosDelMensaje = mysqli_fetch_assoc($resultadoFinal);
    
    mysqli_stmt_close($preparacion);
    mysqli_close($conexionBaseDatos);
    
    return $datosDelMensaje;
}

/**
 * Cambia el estado de un mensaje a 'atendido' y lo marca como leído
 */
function marcarMensajeComoLeido($idReclamacionRecibido) {
    $conexionBaseDatos = obtenerConexion();
    
    $preparacion = mysqli_prepare($conexionBaseDatos, "UPDATE reclamaciones SET leido = 1, estadoReclamacion = 'atendido' WHERE idReclamacion = ?");
    mysqli_stmt_bind_param($preparacion, "i", $idReclamacionRecibido);
    $seEjecutoCorrectamente = mysqli_stmt_execute($preparacion);
    
    mysqli_stmt_close($preparacion);
    mysqli_close($conexionBaseDatos);
    
    return $seEjecutoCorrectamente;
}

/**
 * Guarda una respuesta de texto para un mensaje específico
 */
function responderMensaje($idReclamacionRecibido, $textoDeLaRespuesta) {
    $conexionBaseDatos = obtenerConexion();
    
    // 1. Primero actualizamos la base de datos
    $preparacionUpdate = mysqli_prepare($conexionBaseDatos, "UPDATE reclamaciones SET respuesta = ?, estadoReclamacion = 'atendido', leido = 1 WHERE idReclamacion = ?");
    mysqli_stmt_bind_param($preparacionUpdate, "si", $textoDeLaRespuesta, $idReclamacionRecibido);
    $seActualizoDB = mysqli_stmt_execute($preparacionUpdate);
    mysqli_stmt_close($preparacionUpdate);
    
    // 2. Lógica de notificaciones opcional (si existe el helper de Firebase)
    if ($seActualizoDB == true) {
        $rutaHelperFirebase = __DIR__ . "/../controladores/firebase/firebase_helper.php";
        if (file_exists($rutaHelperFirebase)) {
            // Buscamos datos originales para la notificación
            $sqlBusqueda = "SELECT idEstudiante, idProfesor, emisor_rol, asunto FROM reclamaciones WHERE idReclamacion = $idReclamacionRecibido";
            $resBusqueda = mysqli_query($conexionBaseDatos, $sqlBusqueda);
            $datosOriginales = mysqli_fetch_assoc($resBusqueda);
            
            if (!empty($datosOriginales)) {
                require_once $rutaHelperFirebase;
                $idDestino = 0;
                $rolDestino = "";
                
                if ($datosOriginales['emisor_rol'] == 'estudiante') {
                    $idDestino = $datosOriginales['idEstudiante'];
                    $rolDestino = 'estudiante';
                } else if ($datosOriginales['emisor_rol'] == 'profesor') {
                    $idDestino = $datosOriginales['idProfesor'];
                    $rolDestino = 'profesor';
                }
                
                if ($idDestino > 0) {
                    $tokenDestinatario = obtenerTokenUsuario($idDestino, $rolDestino);
                    if (!empty($tokenDestinatario)) {
                        enviarNotificacionFirebase($tokenDestinatario, "Respuesta a: " . $datosOriginales['asunto'], $textoDeLaRespuesta);
                    }
                }
            }
        }
    }
    
    mysqli_close($conexionBaseDatos);
    return $seActualizoDB;
}

/**
 * Crea un nuevo mensaje en el sistema
 */
function insertarNuevoMensaje($idDelEstudiante, $idDelProfesor, $asuntoMensaje, $descripcionMensaje, $fechaDeEnvio, $rolDelEmisor = 'estudiante') {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "INSERT INTO reclamaciones (idEstudiante, idProfesor, emisor_rol, asunto, descripcion, fecha, estadoReclamacion, leido, respuesta) 
                     VALUES (?, ?, ?, ?, ?, ?, 'pendiente', 0, '')";
                     
    $preparacion = mysqli_prepare($conexionBaseDatos, $sentenciaSQL);
    
    // Si los IDs son 0 o vacíos, los enviamos como null a la base de datos
    $valorEstudiante = !empty($idDelEstudiante) ? $idDelEstudiante : null;
    $valorProfesor = !empty($idDelProfesor) ? $idDelProfesor : null;
    
    mysqli_stmt_bind_param($preparacion, "iissss", $valorEstudiante, $valorProfesor, $rolDelEmisor, $asuntoMensaje, $descripcionMensaje, $fechaDeEnvio);
    $resultadoOperacion = mysqli_stmt_execute($preparacion);
    
    mysqli_stmt_close($preparacion);
    mysqli_close($conexionBaseDatos);
    
    return $resultadoOperacion;
}

/**
 * Lista todos los mensajes enviados/recibidos por un estudiante
 */
function listarMensajesDeEstudiante($idEstudianteRecibido) {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "SELECT r.*, p.nombreProfesor 
                    FROM reclamaciones r
                    LEFT JOIN profesores p ON r.idProfesor = p.idProfesor
                    WHERE r.idEstudiante = $idEstudianteRecibido
                    ORDER BY r.idReclamacion DESC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalMensajes = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalMensajes[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalMensajes;
}

/**
 * Lista todos los mensajes enviados/recibidos por un profesor
 */
function listarMensajesParaProfesor($idProfesorRecibido) {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "SELECT r.*, e.nombreEstudiante 
                    FROM reclamaciones r
                    LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
                    WHERE r.idProfesor = $idProfesorRecibido
                    ORDER BY r.idReclamacion DESC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalMensajes = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalMensajes[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalMensajes;
}

/**
 * Elimina un mensaje permanentemente
 */
function eliminarMensaje($idReclamacionABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM reclamaciones WHERE idReclamacion = $idReclamacionABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    
    return $resultadoOperacion;
}
?>