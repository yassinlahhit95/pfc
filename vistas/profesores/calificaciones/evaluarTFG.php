<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = (int)$_SESSION['idProfesor'];

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$estudiante   = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: tfg.php"); exit;
}

$calificacion = obtenerCalificacionTFG($idEstudiante);

/* Avatar */
$_av_partes    = explode(' ', trim($estudiante['nombreEstudiante']));
$_av_iniciales = mb_strtoupper(mb_substr($_av_partes[0], 0, 1));
if (count($_av_partes) > 1) $_av_iniciales .= mb_strtoupper(mb_substr($_av_partes[1], 0, 1));
$_av_paleta    = ['#4F46E5','#0ea5e9','#10b981','#f59e0b','#ec4899','#8b5cf6'];
$_av_color     = $_av_paleta[ord($_av_iniciales[0]) % count($_av_paleta)];

$tituloDelPagina = "AULAPRO | EVALUAR TFG";
$seccionActual   = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Evaluar TFG</h1>
        <p class="subtitulo-encabezado">Calificación del Trabajo de Fin de Grado</p>
    </div>
    <a href="tfg.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<!-- Ficha del estudiante -->
<div class="panel">
    <div class="perfil-cabecera">
        <div class="perfil-avatar" style="--av-color:<?= $_av_color ?>">
            <?= Security::escapeHtml($_av_iniciales) ?>
        </div>
        <div class="perfil-info">
            <div class="perfil-nombre"><?= Security::escapeHtml(mb_strtoupper($estudiante['nombreEstudiante'], 'UTF-8')) ?></div>
            <div class="perfil-meta">
                <i class="fas fa-graduation-cap"></i>
                <?= Security::escapeHtml($estudiante['nombreCiclo']) ?>
                <span class="perfil-sep"></span>
                <?php if (!empty($calificacion['nota'])): ?>
                <span class="texto-estado <?= $calificacion['nota'] >= 5 ? 'verde' : 'rojo' ?>">
                    Nota actual: <?= Security::escapeHtml($calificacion['nota']) ?>/10
                </span>
                <?php else: ?>
                <span class="texto-estado gris">Sin calificar</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="detalle-grid">
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo"><i class="fas fa-file-pdf"></i> Archivo TFG</div>
            <div class="detalle-fila">
                <span class="detalle-label">Estado entrega</span>
                <span class="detalle-valor">
                    <?php if (!empty($estudiante['archivoTFG'])): ?>
                        <span class="texto-estado verde"><i class="fas fa-check"></i> Entregado</span>
                        &nbsp;
                        <a href="../../../public/uploads/pfc/<?= Security::escapeHtml($estudiante['archivoTFG']) ?>"
                           target="_blank" class="boton-secundario" style="padding:4px 10px;font-size:.8rem;">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    <?php else: ?>
                        <span class="texto-estado rojo"><i class="fas fa-times"></i> No entregado</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de calificación -->
<div class="panel" style="margin-top:20px;">
    <div class="detalle-seccion-titulo" style="padding:0 0 16px;border-bottom:1px solid var(--border);margin-bottom:20px;">
        <i class="fas fa-star"></i> Calificación
    </div>
    <form action="../../../controladores/profesores/pfc/calificar.php" method="POST" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= (int)$idEstudiante ?>">
        <input type="hidden" name="origen" value="calificacionesTFG">

        <div class="campo">
            <label for="nota">Nota (0–10)</label>
            <input type="text" id="nota" name="nota"
                   value="<?= Security::escapeHtml($calificacion['nota'] ?? '') ?>"
                   placeholder="Ej: 7.5">
        </div>

        <div class="campo ancho-total">
            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="3"
                      placeholder="Comentarios sobre el trabajo..."><?= Security::escapeHtml($calificacion['observaciones'] ?? '') ?></textarea>
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
                <div class="notif-bloque-chk">✓</div>
            </label>
        </div>

        <div class="acciones">
            <input type="submit" name="calificarTFG" class="boton-primario" value="Guardar Nota">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
