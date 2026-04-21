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
    $_SESSION['error'] = "Estudiante no encontrado";
    header("Location: verEstudiantes.php");
    exit;
}

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);

$titulo_pagina = "Detalles Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles de Estudiante</h1>
    <a href="/pfc/vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Información Personal</h3>
    </div>
    
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Nombre Completo</label>
            <p class="texto-negrita"><?php echo $estudiante['nombreEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">DNI</label>
            <p class="texto-negrita"><?php echo $estudiante['dniEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Correo Electrónico</label>
            <p class="texto-negrita"><?php echo $estudiante['emailEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Teléfono</label>
            <p class="texto-negrita"><?php echo $estudiante['telefonoEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha Nacimiento</label>
            <p class="texto-negrita"><?php echo $estudiante['fechaNacimientoEstudiante']; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha Alta</label>
            <p class="texto-negrita"><?php echo $estudiante['fechaAltaEstudiante']; ?></p>
        </div>

        <div class="campo-formulario campo-ancho-total">
            <label class="texto-atenuado texto-pequeno">Dirección</label>
            <p class="texto-negrita"><?php 
                if (isset($estudiante['direccionEstudiante'])) { 
                    echo $estudiante['direccionEstudiante']; 
                } else { 
                    echo '-'; 
                } 
            ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Ciudad y CP</label>
            <p class="texto-negrita"><?php 
                $ciudad = '-';
                if (isset($estudiante['ciudadEstudiante'])) {
                    $ciudad = $estudiante['ciudadEstudiante'];
                }
                
                $cp = '-';
                if (isset($estudiante['codigoPostalEstudiante'])) {
                    $cp = $estudiante['codigoPostalEstudiante'];
                }
                
                echo $ciudad . " (" . $cp . ")"; 
            ?></p>
        </div>
    </div>
</div>

<!-- SECCIÓN: TFG -->
<div class="tarjeta-blanca mt-25">
    <div class="titulo-tarjeta">
        <h3>Trabajo Fin de Grado (TFG)</h3>
    </div>
    <div class="formulario-cuadricula">
        <div class="campo-formulario campo-ancho-total">
            <label class="texto-atenuado texto-pequeno">Título del Proyecto</label>
            <p class="texto-negrita"><?php 
                if (isset($estudiante['tituloTFG'])) {
                    echo $estudiante['tituloTFG'];
                } else {
                    echo 'No definido';
                }
            ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Archivo PDF</label>
            <div class="mt-5">
                <?php if (isset($estudiante['archivoTFG']) && !empty($estudiante['archivoTFG'])) { ?>
                    <a href="uploads/tfg/<?php echo $estudiante['archivoTFG']; ?>" target="_blank" class="boton-secundario">
                        <i class="fas fa-file-pdf"></i> Descargar TFG
                    </a>
                <?php } else { ?>
                    <span class="estado-bolita inactivo-rojo">No subido</span>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>