<?php
session_start();
$titulo_pagina = "AULAPRO | DETALLES DIRECTOR";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/directores.php";

$id = $_GET['id'] ?? 0;
$director = obtenerDirectorPorId($id);

if (!$director) { ?>
    <div class='mensaje-error'>Director no encontrado.</div>
    <?php include '../comunes/footer.php';
    exit;
}
?>

<div class="encabezado-pagina">
    <h1>FICHA DEL DIRECTOR</h1>
    <a href="verDirectores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Información General</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= $director['nombreDirector'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $director['emailDirector'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle">
            <?php if (!empty($director['dniDirector'])) { ?>
                <?= $director['dniDirector'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Teléfono</div>
        <div class="valor-detalle"><?= $director['telefonoDirector'] ?: '<span class="texto-atenuado">No especificado</span>' ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle">
            <?php if (isset($director['fechaNacimientoDirector']) && $director['fechaNacimientoDirector'] != '0000-00-00') { ?>
                <?= date('d/m/Y', strtotime($director['fechaNacimientoDirector'])) ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha Alta</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($director['fechaAltaDirector'])) ?></div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Dirección y Contacto</h3>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Dirección</div>
        <div class="valor-detalle"><?= $director['direccionDirector'] ?: '<span class="texto-atenuado">No especificado</span>' ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciudad</div>
        <div class="valor-detalle"><?= $director['ciudadDirector'] ?: '<span class="texto-atenuado">No especificado</span>' ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Código Postal</div>
        <div class="valor-detalle"><?= $director['codigoPostalDirector'] ?: '<span class="texto-atenuado">No especificado</span>' ?></div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Observaciones</h3>
    </div>
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?php if (!empty($director['observacionesDirector'])) { ?>
                <?= $director['observacionesDirector'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">Sin observaciones.</span>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




