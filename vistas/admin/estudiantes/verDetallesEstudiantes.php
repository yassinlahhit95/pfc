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

$titulo_pagina = "Detalle del Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Ficha de Estudiante</h1>
    <div class="acciones-pagina">
        <a href="../../../vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=<?= $idDelEstudiante ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Datos
        </a>
        <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>InformaciÃ³n Personal</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= $estudiante['nombreEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $estudiante['emailEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle"><?= $estudiante['dniEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">TelÃ©fono</div>
        <div class="valor-detalle"><?= $estudiante['telefonoEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciclo Formativo</div>
        <div class="valor-detalle"><span class="estado-bolita activo-verde"><?= $estudiante['nombreCiclo'] ?></span></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante'])) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciudad / DirecciÃ³n</div>
        <div class="valor-detalle"><?= $estudiante['direccionEstudiante'] . ", " . $estudiante['ciudadEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha de Alta</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($estudiante['fechaAltaEstudiante'])) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Observaciones</div>
        <div class="valor-detalle"><?= !empty($estudiante['observacionesEstudiante']) ? $estudiante['observacionesEstudiante'] : '<span class="texto-atenuado">Sin observaciones</span>' ?></div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>SituaciÃ³n del TFG</h3>
    </div>
    <div class="disposicion-flexible alinear-centro espacio-entre-elementos">
        <div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="estado-bolita activo-verde">Entregado</span>
                <p class="texto-pequeno texto-atenuado mt-5">Subido el: <?= date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG'])) ?></p>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">No subido</span>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



