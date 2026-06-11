<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idEstudiante = $_GET['idEstudiante'] ?? 0;
$idReto       = $_GET['idReto']       ?? 0;
$idCiclo      = $_GET['idCiclo']      ?? 0;

$estudiante = obtenerEstudiantePorId($idEstudiante);
$reto       = obtenerRetoPorId($idReto);

if (!$estudiante || !$reto) {
    header("Location: calificacionesRetos.php");
    exit;
}

$notaActual = obtenerCalificacionReto($idEstudiante, $idReto);

$tituloDelPagina = "AULAPRO | EVALUAR RETO";
$seccionActual = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUAR RETO</h1>
    <a href="calificacionesRetos.php?idReto=<?= Security::escapeHtml($idReto ) ?>&idCiclo=<?= Security::escapeHtml($idCiclo ) ?>" class="boton-secundario">VOLVER</a>
</div>

<?php if ($errores && !is_array($errores)) { ?><div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div><?php } ?>
<?php if ($exito)   { ?><div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div><?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><?= Security::escapeHtml(strtoupper($estudiante['nombreEstudiante'])) ?></h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciclo</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['nombreCiclo'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Reto</div>
        <div class="valor-detalle texto-negrita"><?= Security::escapeHtml($reto['nombreReto'] ) ?></div>
    </div>

    <?php if ($notaActual !== '') { ?>
    <div class="fila-datos">
        <div class="nombre-detalle">Nota actual</div>
        <div class="valor-detalle texto-negrita <?= Security::escapeHtml($notaActual >= 5 ? 'texto-verde' : 'texto-rojo') ?>">
            <?= Security::escapeHtml($notaActual ) ?> / 10
        </div>
    </div>
    <?php } ?>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>CALIFICACIÓN</h3>
    </div>

    <form action="../../../controladores/profesores/academico/calificarRetoUnico.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml((int)$idEstudiante) ?>">
        <input type="hidden" name="idReto"       value="<?= Security::escapeHtml((int)$idReto) ?>">
        <input type="hidden" name="idCiclo"      value="<?= Security::escapeHtml((int)$idCiclo) ?>">

        <div class="campo">
            <label>Nota (0-10) — dejar vacío para eliminar</label>
            <input type="text" name="nota" value="<?= Security::escapeHtml($notaActual ) ?>" placeholder="Introduce la nota (0-10)" class="<?= Security::escapeHtml(!empty($errores['nota']) ? 'input-error' : '') ?>">
            <?php if (!empty($errores['nota'])) { ?><span class="error-campo"><?= Security::escapeHtml($errores['nota'] ) ?></span><?php } ?>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarNota" class="boton-primario" value="Guardar Nota">
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


