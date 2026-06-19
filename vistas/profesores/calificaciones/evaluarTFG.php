<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (empty($idProfesor)) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: tfg.php");
    exit;
}

// Opcional: Validar que el estudiante pertenece a un ciclo del profesor
// Por ahora seguimos la lógica del admin pero adaptada

$calificacion = obtenerCalificacionTFG($idEstudiante);

$titulo_pagina = "AULAPRO | EVALUAR TFG";
$seccion = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUAR TFG</h1>
    <a href="tfg.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-graduate"></i> <?= Security::escapeHtml(strtoupper($estudiante['nombreEstudiante'])) ?></h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciclo</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['nombreCiclo'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Archivo TFG</div>
        <div class="valor-detalle">
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="indicador-estado activo-verde">ENTREGADO</span>
                <a href="../../../public/uploads/pfc/<?= Security::escapeHtml($estudiante['archivoTFG'] ) ?>" target="_blank" class="btn-accion btn-ver" style="margin-left: 10px;">
                    <i class="fas fa-file-pdf"></i> Descargar
                </a>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo">NO ENTREGADO</span>
            <?php } ?>
        </div>
    </div>

    <?php if (!empty($calificacion)) { ?>
    <div class="fila-datos">
        <div class="nombre-detalle">Nota actual</div>
        <div class="valor-detalle texto-negrita <?= Security::escapeHtml($calificacion['nota'] >= 5 ? 'texto-verde' : 'texto-rojo') ?>">
            <?= Security::escapeHtml($calificacion['nota'] ) ?> / 10
        </div>
    </div>
    <?php } ?>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-edit"></i> CALIFICACIÓN</h3>
    </div>

    <form action="../../../controladores/profesores/pfc/calificar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml((int)$idEstudiante) ?>">
        <input type="hidden" name="origen" value="calificacionesTFG">

        <div class="campo">
            <label>Nota (0-10)</label>
            <input type="text" name="nota" value="<?= Security::escapeHtml($calificacion['nota'] ?? '') ?>" placeholder="Ej: 7.5">
        </div>

        <div class="campo">
            <label>Observaciones</label>
            <textarea name="observaciones" rows="3"><?= Security::escapeHtml($calificacion['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="campo">
            <label class="campo-checkbox">
                <input type="checkbox" name="notificarEstudiante" value="1" checked>
                <b>Notificar al estudiante (Email + Push)</b>
            </label>
        </div>

        <div class="acciones">
            <input type="submit" name="calificarTFG" class="boton-primario" value="Guardar Nota">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


