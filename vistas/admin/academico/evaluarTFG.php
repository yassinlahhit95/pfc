<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: calificacionesTFG.php");
    exit;
}

$calificacion = obtenerCalificacionTFG($idEstudiante);

$titulo_pagina = "AULAPRO | EVALUAR TFG";
$seccion = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUAR TFG</h1>
    <a href="calificacionesTFG.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="grid-2col">
    <!-- Panel Izquierdo: Info del alumno -->
    <div class="panel">
        <div style="display:flex; align-items:center; gap:20px; margin-bottom: 25px;">
            <div style="width:60px;height:60px;font-size:1.4rem;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--accent);color:#fff;font-weight:700;flex-shrink:0;">
                <?= mb_strtoupper(substr(Security::escapeHtml($estudiante['nombreEstudiante']), 0, 2)) ?>
            </div>
            <div>
                <h2 style="margin:0;font-size:1.3rem; font-weight:700; color:var(--text);">
                    <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
                </h2>
                <div class="texto-suave" style="font-size: 0.95rem; margin-top: 4px; display:flex; align-items:center; gap:5px;">
                    <i class="fas fa-graduation-cap" style="color:var(--accent)"></i> <?= Security::escapeHtml($estudiante['nombreCiclo']) ?>
                </div>
            </div>
        </div>

        <div class="campo" style="background:var(--surface-2); border-radius:12px; padding:16px; margin-bottom:15px;">
            <div style="font-size:0.75rem; color:var(--dim); margin-bottom:10px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Estado del Proyecto</div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <div style="display:flex; align-items:center; justify-content:space-between; gap:15px; flex-wrap:wrap;">
                    <div>
                        <span class="texto-estado verde"><i class="fas fa-check-circle"></i> ENTREGADO</span>
                        <?php if (!empty($estudiante['tituloTFG'])): ?>
                            <div class="texto-suave" style="margin-top:10px; font-style:italic; font-size:0.95rem; border-left: 3px solid var(--accent); padding-left:10px;">
                                <i class="fas fa-quote-left" style="opacity:0.5; margin-right:5px;"></i> <?= Security::escapeHtml($estudiante['tituloTFG']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="../../../controladores/comunes/verTFG.php?id=<?= Security::escapeHtml($estudiante['idEstudiante'] ) ?>&modo=descarga" target="_blank" class="boton-secundario">
                        <i class="fas fa-file-pdf"></i> Ver PDF
                    </a>
                </div>
            <?php } else { ?>
                <span class="texto-estado rojo"><i class="fas fa-times-circle"></i> NO ENTREGADO</span>
            <?php } ?>
        </div>

        <?php if (!empty($calificacion)) { ?>
        <div class="campo" style="display:flex; align-items:center; justify-content:space-between; background:var(--surface-2); border-radius:12px; padding:16px;">
            <div style="font-size:0.85rem; color:var(--dim); text-transform:uppercase; letter-spacing:1px; font-weight:600;">Calificación Actual</div>
            <div class="texto-negrita <?= $calificacion['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>" style="font-size:2rem;">
                <?= $calificacion['nota'] ?>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Panel Derecho: Formulario de Calificación -->
    <div class="panel">
        <h3 style="margin-top:0; margin-bottom:25px; font-size:1.2rem; color:var(--accent); display:flex; align-items:center; gap:10px;">
            <i class="fas fa-edit"></i> Formulario de Calificación
        </h3>
        <form action="../../../controladores/admin/pfc/calificar.php" method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= (int)$idEstudiante ?>">

            <div class="campo ancho-total">
                <label for="nota">Nota Definitiva (0 - 10)</label>
                <input type="number" step="0.01" min="0" max="10" id="nota" name="nota" value="<?= Security::escapeHtml($calificacion['nota'] ?? '') ?>" placeholder="Ej: 7.5" style="font-size:1.4rem; font-weight:bold; color:var(--accent); text-align:center;">
                <div class="texto-suave" style="font-size:0.8rem; margin-top:8px;"><i class="fas fa-info-circle"></i> Dejar en blanco para retirar la calificación.</div>
            </div>

            <div class="campo ancho-total">
                <label for="observaciones">Observaciones / Feedback</label>
                <textarea id="observaciones" name="observaciones" rows="4" placeholder="Comentarios constructivos para el alumno..."><?= Security::escapeHtml($calificacion['observaciones'] ?? '') ?></textarea>
            </div>

            <div class="campo ancho-total">
                <label class="notif-bloque">
                    <input type="checkbox" name="notificarEstudiante" value="1" checked>
                    <div class="notif-bloque-icono"><i class="fas fa-bell"></i></div>
                    <div class="notif-bloque-texto">
                        <div class="notif-bloque-titulo">Notificar al estudiante</div>
                        <div class="notif-bloque-canales">
                            <span class="notif-canal"><i class="fas fa-envelope"></i> Email</span>
                            <span class="notif-canal"><i class="fas fa-mobile-alt"></i> Push</span>
                        </div>
                    </div>
                    <div class="notif-bloque-chk"><i class="fas fa-check"></i></div>
                </label>
            </div>

            <div class="acciones">
                <button type="submit" name="calificarTFG" class="boton-primario">
                    <i class="fas fa-save"></i> Guardar Calificación
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

