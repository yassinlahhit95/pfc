<?php
session_start();
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idDelEstudiante = 0;
$idDelEstudiante = ($_GET['idEstudiante'] ?? 0);

$estudiante = obtenerEstudiantePorId($idDelEstudiante);

if (!$estudiante) {
    header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
    exit;
}

$titulo_pagina = "AULAPRO | DETALLE DEL ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>FICHA DE ESTUDIANTE</h1>
    <div class="acciones-pagina">
        <a href="../../../vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=<?= $idDelEstudiante ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Datos
        </a>
        <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Información Personal</h3>
    </div>
    
    <div class="fila-dat">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= $estudiante['nombreEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $estudiante['emailEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle"><?= $estudiante['dniEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Teléfono</div>
        <div class="valor-detalle"><?= $estudiante['telefonoEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Ciclo Formativo</div>
        <div class="valor-detalle"><span class="bolita activo-verde"><?= $estudiante['nombreCiclo'] ?></span></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante'])) ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Ciudad / Dirección</div>
        <div class="valor-detalle"><?= $estudiante['direccionEstudiante'] . ", " . $estudiante['ciudadEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Fecha de Alta</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($estudiante['fechaAltaEstudiante'])) ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Observaciones</div>
        <div class="valor-detalle"><?= !empty($estudiante['observacionesEstudiante']) ? $estudiante['observacionesEstudiante'] : '<span class="atenuado">Sin observaciones</span>' ?></div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Situación del TFG</h3>
    </div>
    <div class="d-flex alinear-centro espacio-entre-elementos">
        <div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="bolita activo-verde">Entregado</span>
                <p class="texto-pequeno atenuado" style="margin-top: 5px;">Subido el: <?= date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG'])) ?></p>
            <?php } else { ?>
                <span class="bolita inactivo-rojo">No subido</span>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>





