<?php
session_start();

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = $_GET['idProfesor'] ?? 0;

$profesor = obtenerProfesorPorId($idProfesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$modulosProfesor = listarModulosDeProfesor($idProfesor);
$ciclosTutorizados = listarCiclosTutorizadosProfesor($idProfesor);

$titulo_pagina = "AULAPRO | DETALLES PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>FICHA DEL PROFESOR</h1>
    <a href="../../../vistas/admin/profesores/verProfesores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Información General</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= $profesor['nombreProfesor'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Email</div>
        <div class="valor-detalle"><?= $profesor['emailProfesor'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">DNI</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['dniProfesor'])) { ?>
                <?= $profesor['dniProfesor'] ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Teléfono</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['telefonoProfesor'])) { ?>
                <?= $profesor['telefonoProfesor'] ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['fechaNacimientoProfesor']) && $profesor['fechaNacimientoProfesor'] != '0000-00-00') { ?>
                <?= date('d/m/Y', strtotime($profesor['fechaNacimientoProfesor'])) ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha Alta</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['fechaAltaProfesor'])) { ?>
                <?= date('d/m/Y', strtotime($profesor['fechaAltaProfesor'])) ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Dirección y Contacto</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Dirección</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['direccionProfesor'])) { ?>
                <?= $profesor['direccionProfesor'] ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciudad</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['ciudadProfesor'])) { ?>
                <?= $profesor['ciudadProfesor'] ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Código Postal</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['codigoPostalProfesor'])) { ?>
                <?= $profesor['codigoPostalProfesor'] ?>
            <?php } else { ?>
                <span class="texto-suave">No especificado</span>
            <?php } ?>
        </div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Observaciones</h3>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['observacionesProfesor'])) { ?>
                <?= $profesor['observacionesProfesor'] ?>
            <?php } else { ?>
                <span class="texto-suave">Sin observaciones.</span>
            <?php } ?>
        </div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Ciclos Tutorizados</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre del Ciclo</th>
                    <th>Nivel</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ciclosTutorizados)) { ?>
                    <tr><td colspan="2" class="vacio">No es tutor de ningún ciclo</td></tr>
                <?php } else { ?>
                    <?php foreach ($ciclosTutorizados as $cicloItem) { ?>
                    <tr>
                        <td><b><?= $cicloItem['nombreCiclo'] ?></b></td>
                        <td><?= $cicloItem['nombreNivel'] ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel margen-arriba">
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
                    <tr><td colspan="2" class="vacio">No tiene módulos asignados</td></tr>
                <?php } else { ?>
                    <?php foreach ($modulosProfesor as $moduloItem) { ?>
                    <tr>
                        <td><b><?= $moduloItem['nombreModulo'] ?></b></td>
                        <td><span class="texto-estado azul"><?= $moduloItem['abreviaturaCiclo'] ?></span></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
