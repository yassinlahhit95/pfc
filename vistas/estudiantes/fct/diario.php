<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

$exito  = $_SESSION['exito']  ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idEstudiante = $_SESSION['idEstudiante'];
$con = obtenerConexion();

// Obtiene los detalles del FCT
$stmt = mysqli_prepare($con, "
    SELECT f.*, p.nombreProfesor, c.nombreCiclo 
    FROM fct f
    LEFT JOIN profesores p ON f.idProfesorTutor = p.idProfesor
    LEFT JOIN ciclos c ON f.idCiclo = c.idCiclo
    WHERE f.idEstudiante = ? LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
mysqli_stmt_execute($stmt);
$fct = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$diarios = [];
$horasAprobadas = 0.0;
$horasPendientes = 0.0;
$tienePendientes = false;

if ($fct) {
    $idFCT = $fct['idFCT'];
    
    // Obtiene los logs
    $stmtD = mysqli_prepare($con, "SELECT * FROM fct_diarios WHERE idFCT = ? ORDER BY fecha DESC, idDiario DESC");
    mysqli_stmt_bind_param($stmtD, "i", $idFCT);
    mysqli_stmt_execute($stmtD);
    $diarios = mysqli_fetch_all(mysqli_stmt_get_result($stmtD), MYSQLI_ASSOC);

    foreach ($diarios as $d) {
        if ($d['estado'] === 'aprobado') {
            $horasAprobadas += (float)$d['horas'];
        } elseif ($d['estado'] === 'pendiente') {
            $horasPendientes += (float)$d['horas'];
            $tienePendientes = true;
        }
    }
}

$tituloDelPagina = "AULAPRO | MI DIARIO DE FCT";
$seccionActual = 'fct_diario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MI DIARIO DE FCT / DUAL</h1>
        <p class="subtitulo-encabezado">Registra tus horas diarias de prácticas y solicita la aprobación de tu tutor de empresa.</p>
    </div>
</div>

<?php if ($exito): ?>
    <div class="alerta alerta-exito margen-abajo">
        <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
    </div>
<?php endif; ?>

<?php if ($errores): ?>
    <div class="alerta alerta-error margen-abajo">
        <i class="fas fa-exclamation-circle"></i> <?= Security::escapeHtml($errores) ?>
    </div>
<?php endif; ?>

<?php if (!$fct): ?>
    <div class="panel">
        <div class="vacio">
            <i class="fas fa-briefcase fa-3x" style="color:var(--dim);margin-bottom:15px;"></i>
            <h3>Sin asignación de prácticas</h3>
            <p>Todavía no tienes una asignación de Formación en Centros de Trabajo (FCT) configurada por tu centro de estudios.</p>
        </div>
    </div>
<?php else: ?>
    <!-- Stats Banner -->
    <div class="columnas margen-abajo" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
        <div class="caja-estadistica panel">
            <div class="info">
                <span class="label">Empresa</span>
                <span class="numero" style="font-size:1.3rem;"><?= Security::escapeHtml($fct['empresa']) ?></span>
                <span class="tendencia"><i class="fas fa-user-tie"></i> Tutor: <?= Security::escapeHtml($fct['tutorEmpresa'] ?? 'Sin asignar') ?></span>
            </div>
        </div>
        <div class="caja-estadistica panel">
            <div class="info">
                <span class="label">Horas Requeridas</span>
                <span class="numero"><?= (int)$fct['horasTotales'] ?> h</span>
                <span class="tendencia"><i class="fas fa-clock"></i> Total exigido</span>
            </div>
        </div>
        <div class="caja-estadistica panel">
            <div class="info">
                <span class="label">Horas Aprobadas</span>
                <span class="numero verde"><?= $horasAprobadas ?> h</span>
                <span class="tendencia verde"><i class="fas fa-check"></i> <?= round(($horasAprobadas / max(1, $fct['horasTotales'])) * 100) ?>% completado</span>
            </div>
        </div>
        <div class="caja-estadistica panel">
            <div class="info">
                <span class="label">Horas Pendientes</span>
                <span class="numero naranja"><?= $horasPendientes ?> h</span>
                <span class="tendencia naranja"><i class="fas fa-hourglass-half"></i> En espera de firma</span>
            </div>
        </div>
    </div>

    <div class="columnas" style="grid-template-columns: 1fr 2fr; gap: 20px; align-items: start;">
        <!-- Left Column: Add Entry -->
        <div class="panel">
            <h2>Nuevo registro diario</h2>
            <form action="../../../controladores/estudiantes/fct/guardarLog.php" method="POST" class="formulario">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idFCT" value="<?= (int)$idFCT ?>">

                <div class="campo">
                    <label for="fechaLog">Fecha de la jornada</label>
                    <input type="date" name="fecha" id="fechaLog" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="campo">
                    <label for="horasLog">Horas realizadas</label>
                    <input type="number" name="horas" id="horasLog" step="0.25" min="0.25" max="24" required placeholder="p.ej. 8">
                </div>

                <div class="campo">
                    <label for="actividadesLog">Descripción de actividades realizadas</label>
                    <textarea name="actividades" id="actividadesLog" rows="5" required placeholder="Detalla brevemente las tareas de hoy..."></textarea>
                </div>

                <div class="acciones">
                    <button type="submit" class="boton-primario" style="width:100%;"><i class="fas fa-plus"></i> Guardar Jornada</button>
                </div>
            </form>
        </div>

        <!-- Right Column: Log History -->
        <div class="panel">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <h2 style="margin:0;">Historial de actividades</h2>
                <?php if ($tienePendientes && !empty($fct['emailTutorEmpresa'])): ?>
                    <form action="../../../controladores/estudiantes/fct/solicitarFirma.php" method="POST" style="margin:0;">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <input type="hidden" name="idFCT" value="<?= (int)$idFCT ?>">
                        <button type="submit" class="boton-naranja"><i class="fas fa-paper-plane"></i> ENVIAR DIARIO AL TUTOR PARA FIRMAR</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Horas</th>
                            <th>Actividades</th>
                            <th>Estado</th>
                            <th>Tutor Feedback / Obs</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($diarios)): ?>
                            <tr>
                                <td colspan="6" class="vacio">No has registrado ninguna jornada de prácticas todavía.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($diarios as $d): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                                    <td><strong><?= (float)$d['horas'] ?> h</strong></td>
                                    <td><?= nl2br(Security::escapeHtml($d['actividades'])) ?></td>
                                    <td>
                                        <?php if ($d['estado'] === 'aprobado'): ?>
                                            <span class="texto-estado verde"><i class="fas fa-check"></i> Aprobado</span>
                                        <?php elseif ($d['estado'] === 'rechazado'): ?>
                                            <span class="texto-estado rojo"><i class="fas fa-times"></i> Rechazado</span>
                                        <?php else: ?>
                                            <span class="texto-estado naranja"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-size:0.85rem; color:var(--text-2);"><?= Security::escapeHtml($d['observacionesTutor'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <?php if ($d['estado'] === 'pendiente'): ?>
                                            <form action="../../../controladores/estudiantes/fct/eliminarLog.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');" style="margin:0;">
                                                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                                <input type="hidden" name="idDiario" value="<?= (int)$d['idDiario'] ?>">
                                                <button type="submit" class="btn-accion btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
