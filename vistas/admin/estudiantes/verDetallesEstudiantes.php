<?php
session_start();
require_once "../../../modelos/conectar.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/ciclos.php";

$idDelEstudiante = 0;
if (isset($_GET['idEstudiante'])) {
    $idDelEstudiante = $_GET['idEstudiante'];
}

$estudiante = obtenerEstudiantePorId($idDelEstudiante);

if (!$estudiante) {
    header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
    exit;
}

$titulo_pagina = "Detalle del Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Ficha de Estudiante</h1>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=<?php echo $idDelEstudiante; ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Datos
        </a>
        <a href="/pfc/vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Información Personal</h3>
    </div>
    
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Nombre Completo</label>
            <p class="texto-grande texto-negrita"><?php echo $estudiante['nombreEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Email</label>
            <p><?php echo $estudiante['emailEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">DNI</label>
            <p><?php echo $estudiante['dniEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Teléfono</label>
            <p><?php echo $estudiante['telefonoEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Ciclo Formativo</label>
            <p class="etiqueta-gris"><?php echo $estudiante['nombreCiclo']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha de Nacimiento</label>
            <p><?php echo date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante'])); ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Ciudad / Dirección</label>
            <p><?php echo $estudiante['direccionEstudiante'] . ", " . $estudiante['ciudadEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha de Alta en el Centro</label>
            <p><?php echo date('d/m/Y', strtotime($estudiante['fechaAltaEstudiante'])); ?></p>
        </div>
    </div>

    <div class="margen-arriba-grande pt-20" style="border-top: 1px solid #eee;">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label class="texto-atenuado texto-pequeno">Observaciones</label>
                <p><?php echo $estudiante['observacionesEstudiante']; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Situación del TFG</h3>
    </div>
    <div class="disposicion-flexible alinear-centro espacio-entre-elementos">
        <div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="estado-bolita activo-verde">Entregado</span>
                <p class="texto-pequeno texto-atenuado mt-5">Subido el: <?php echo date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG'])); ?></p>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">No subido</span>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

