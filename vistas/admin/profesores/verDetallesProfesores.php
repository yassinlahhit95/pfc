<?php
session_start();
$titulo_pagina = "AULAPRO | DETALLES PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$id = $_GET['idProfesor'] ?? $_GET['id'] ?? 0;

$profesor = obtenerProfesorPorId($id);

if (!$profesor) {
    echo "<div class='mensaje-error'>Profesor no encontrado. ID recibido: $id</div>";
    include '../comunes/footer.php';
    exit;
}

$ciclosProfesor = obtenerCiclosDeProfesor($id);
$modulosProfesor = obtenerModulosDeProfesor($id);
$retosProfesor = obtenerRetosDeProfesor($id);
?>

<div class="encabezado-pagina">
    <h1>FICHA DEL PROFESOR</h1>
    <a href="../../../vistas/admin/profesores/verProfesores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="tarjeta-blanca margen-abajo">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-tie"></i> Información General</h3>
    </div>
    
    <div class="disposicion-flexible separacion-grande envoltura-flexible">
        <div class="flexible-rellenar">
            <div class="fila-detalle">
                <div class="etiqueta-detalle">Nombre Completo</div>
                <div class="valor-detalle texto-negrita"><?= $profesor['nombreProfesor'] ?></div>
            </div>

            <div class="fila-detalle">
                <div class="etiqueta-detalle">Email</div>
                <div class="valor-detalle"><?= $profesor['emailProfesor'] ?></div>
            </div>

            <div class="fila-detalle">
                <div class="etiqueta-detalle">Teléfono</div>
                <div class="valor-detalle"><?= $profesor['telefonoProfesor'] ?></div>
            </div>

            <div class="fila-detalle">
                <div class="etiqueta-detalle">DNI</div>
                <div class="valor-detalle"><?= $profesor['dniProfesor'] ?></div>
            </div>

            <div class="fila-detalle">
                <div class="etiqueta-detalle">Dirección</div>
                <div class="valor-detalle"><?= $profesor['direccionProfesor'] ?></div>
            </div>
        </div>

        <div class="ancho-fijo-300">
            <div class="mb-20">
                <h4 class="mb-10"><i class="fas fa-layer-group"></i> Ciclos</h4>
                <div class="lista-detalles-lateral">
                    <?php if (empty($ciclosProfesor)) { ?>
                        <p class="texto-atenuado">Sin ciclos asignados</p>
                    <?php } else { ?>
                        <?php foreach ($ciclosProfesor as $c) { ?>
                            <div class="item-detalle-lateral item-detalle-lateral-amarillo">
                                <strong><?= $c['abreviaturaCiclo'] ?></strong><br>
                                <small><?= $c['nombreCiclo'] ?></small>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 class="mb-10"><i class="fas fa-tasks"></i> Retos Activos</h4>
                <div class="lista-detalles-lateral">
                    <?php if (empty($retosProfesor)) { ?>
                        <p class="texto-atenuado">Sin retos asignados</p>
                    <?php } else { ?>
                        <?php foreach ($retosProfesor as $r) { ?>
                            <div class="item-detalle-lateral item-detalle-lateral-azul">
                                <strong><?= $r['nombreReto'] ?></strong><br>
                                <small><?= $r['horasReto'] ?> horas</small>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-book"></i> Módulos Impartidos</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Abreviatura Ciclo</th>
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




