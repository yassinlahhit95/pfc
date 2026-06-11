<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idAdmin'])) {
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

$titulo_pagina = "AULAPRO | EVALUAR RETO";
$seccion = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUAR RETO</h1>
    <a href="calificacionesRetos.php?idReto=<?= $idReto ?>&idCiclo=<?= $idCiclo ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?><div class="mensaje-error"><?= $errores ?></div><?php } ?>
<?php if ($exito)   { ?><div class="mensaje-exito"><?= $exito ?></div><?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-graduate"></i> <?= strtoupper($estudiante['nombreEstudiante']) ?></h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciclo</div>
        <div class="valor-detalle"><?= $estudiante['nombreCiclo'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Reto</div>
        <div class="valor-detalle texto-negrita"><?= $reto['nombreReto'] ?></div>
    </div>

    <?php if ($notaActual !== '') { ?>
    <div class="fila-datos">
        <div class="nombre-detalle">Nota actual</div>
        <div class="valor-detalle texto-negrita <?= $notaActual >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
            <?= $notaActual ?> / 10
        </div>
    </div>
    <?php } ?>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-edit"></i> CALIFICACIÓN</h3>
    </div>

    <form action="../../../controladores/admin/academico/calificarRetoUnico.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= (int)$idEstudiante ?>">
        <input type="hidden" name="idReto"       value="<?= (int)$idReto ?>">
        <input type="hidden" name="idCiclo"      value="<?= (int)$idCiclo ?>">

        <div class="campo">
            <label>Nota (0-10) — dejar vacío para eliminar</label>
            <input type="text" name="nota" value="<?= $notaActual ?>" placeholder="Ej: 7.5">
        </div>

        <div class="acciones">
            <input type="submit" name="guardarNota" class="boton-primario" value="Guardar Nota">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
