<?php
session_start();
$titulo_pagina = "AULAPRO | DETALLES PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id = $_GET['idProfesor'] ?? 0;

$profesor = obtenerProfesorPorId($id);

if (!$profesor) {
    echo "<div class='mensaje-error'>Profesor no encontrado.</div>";
    include '../comunes/footer.php';
    exit;
}

$modulosProfesor = obtenerModulosDeProfesor($id);
?>

<div class="encabezado-pagina">
    <h1>FICHA DEL PROFESOR</h1>
    <a href="../../../vistas/admin/profesores/verProfesores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Información General</h3>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= $profesor['nombreProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $profesor['emailProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['dniProfesor'])) { ?>
                <?= $profesor['dniProfesor'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Teléfono</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['telefonoProfesor'])) { ?>
                <?= $profesor['telefonoProfesor'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['fechaNacimientoProfesor']) && $profesor['fechaNacimientoProfesor'] != '0000-00-00') { ?>
                <?= date('d/m/Y', strtotime($profesor['fechaNacimientoProfesor'])) ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha Alta</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['fechaAltaProfesor'])) { ?>
                <?= date('d/m/Y', strtotime($profesor['fechaAltaProfesor'])) ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Dirección y Contacto</h3>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Dirección</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['direccionProfesor'])) { ?>
                <?= $profesor['direccionProfesor'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciudad</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['ciudadProfesor'])) { ?>
                <?= $profesor['ciudadProfesor'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Código Postal</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['codigoPostalProfesor'])) { ?>
                <?= $profesor['codigoPostalProfesor'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">No especificado</span>
            <?php } ?>
        </div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Observaciones</h3>
    </div>
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['observacionesProfesor'])) { ?>
                <?= $profesor['observacionesProfesor'] ?>
            <?php } else { ?>
                <span class="texto-atenuado">Sin observaciones.</span>
            <?php } ?>
        </div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Módulos Impartidos</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Ciclo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($modulosProfesor)) { ?>
                    <tr><td colspan="2" class="sin-datos">No tiene módulos asignados</td></tr>
                <?php } else { ?>
                    <?php foreach ($modulosProfesor as $m) { ?>
                    <tr>
                        <td><strong><?= $m['nombreModulo'] ?></strong></td>
                        <td><span class="etiqueta-estado azul"><?= $m['abreviaturaCiclo'] ?></span></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
