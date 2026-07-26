<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$idReto = (int)($_GET['idReto'] ?? 0);
$idCiclo = (int)($_GET['idCiclo'] ?? 0);

$estudiante = obtenerEstudiantePorId($idEstudiante);
$reto       = obtenerRetoPorId($idReto);

if (!$estudiante || !$reto) {
    header("Location: calificacionesRetos.php");
    exit;
}

$notaActual = obtenerCalificacionReto($idEstudiante, $idReto);

$titulo_pagina = "AULAPRO | EVALUAR RETO";
$seccion = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUAR RETO</h1>
    <a href="calificacionesRetos.php?idReto=<?= (int)$idReto ?>&idCiclo=<?= (int)$idCiclo ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
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
            <div style="font-size:0.75rem; color:var(--dim); margin-bottom:10px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Reto</div>
            <div class="texto-negrita" style="font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                <i class="fas fa-tasks" style="color:var(--accent)"></i> <?= Security::escapeHtml($reto['nombreReto']) ?>
            </div>
        </div>

        <?php if ($notaActual !== '') { ?>
        <div class="campo" style="display:flex; align-items:center; justify-content:space-between; background:var(--surface-2); border-radius:12px; padding:16px;">
            <div style="font-size:0.85rem; color:var(--dim); text-transform:uppercase; letter-spacing:1px; font-weight:600;">Nota Actual</div>
            <div class="texto-negrita <?= $notaActual >= 5 ? 'texto-verde' : 'texto-rojo' ?>" style="font-size:2rem;">
                <?= Security::escapeHtml($notaActual) ?>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Panel Derecho: Formulario de Calificación -->
    <div class="panel">
        <h3 style="margin-top:0; margin-bottom:25px; font-size:1.2rem; color:var(--accent); display:flex; align-items:center; gap:10px;">
            <i class="fas fa-edit"></i> Formulario de Calificación
        </h3>
        <form action="../../../controladores/admin/academico/calificarRetoUnico.php" method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= (int)$idEstudiante ?>">
            <input type="hidden" name="idReto"       value="<?= (int)$idReto ?>">
            <input type="hidden" name="idCiclo"      value="<?= (int)$idCiclo ?>">

            <div class="campo ancho-total">
                <label for="nota">Nota Definitiva (0 - 10)</label>
                <input type="number" step="0.01" min="0" max="10" id="nota" name="nota" value="<?= Security::escapeHtml((string)$notaActual) ?>" placeholder="Ej: 7.5" style="font-size:1.4rem; font-weight:bold; color:var(--accent); text-align:center;">
                <div class="texto-suave" style="font-size:0.8rem; margin-top:8px;"><i class="fas fa-info-circle"></i> Dejar en blanco para retirar la calificación.</div>
            </div>

            <div class="acciones">
                <button type="submit" name="guardarNota" class="boton-primario">
                    <i class="fas fa-save"></i> Guardar Calificación
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
