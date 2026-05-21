<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/directores.php";

$idDirector = $_GET['id'] ?? 0;
$director = obtenerDirectorPorId($idDirector);

if (!$director) {
    header("Location: verDirectores.php");
    exit;
}

$titulo_pagina = "AULAPRO | DETALLES DIRECTOR";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>FICHA DEL DIRECTOR</h1>
    <a href="verDirectores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Información General</h3>
    </div>
    
    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= $director['nombreDirector'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Email</div>
        <div class="valor-detalle"><?= $director['emailDirector'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">DNI</div>
        <div class="valor-detalle">
            <?php if (!empty($director['dniDirector'])) { ?>
                <?= $director['dniDirector'] ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Teléfono</div>
        <div class="valor-detalle"><?= $director['telefonoDirector'] ?: '<span class="texto-suave">No especificado</span>' ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle">
            <?php if (isset($director['fechaNacimientoDirector']) && $director['fechaNacimientoDirector'] != '0000-00-00') { ?>
                <?= date('d/m/Y', strtotime($director['fechaNacimientoDirector'])) ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha Alta</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($director['fechaAltaDirector'])) ?></div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Dirección y Contacto</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Dirección</div>
        <div class="valor-detalle"><?= $director['direccionDirector'] ?: '<span class="texto-suave">No especificado</span>' ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciudad</div>
        <div class="valor-detalle"><?= $director['ciudadDirector'] ?: '<span class="texto-suave">No especificado</span>' ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Código Postal</div>
        <div class="valor-detalle"><?= $director['codigoPostalDirector'] ?: '<span class="texto-suave">No especificado</span>' ?></div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Observaciones</h3>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?php if (!empty($director['observacionesDirector'])) { ?>
                <?= $director['observacionesDirector'] ?>
            <?php } else { ?>
                <span class="texto-suave">Sin observaciones.</span>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




