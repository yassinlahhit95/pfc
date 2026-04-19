<?php
session_start();
require_once "../../modelos/conectar.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/ciclos.php";

$idDelEstudiante = $_GET['idEstudiante'] ?? 0;
$estudiante = obtenerEstudiantePorId($idDelEstudiante);

if (!$estudiante) {
    $_SESSION['error'] = "Estudiante no encontrado";
    header("Location: verEstudiantes.php");
    exit;
}

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);

$titulo_pagina = "Detalles Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Detalles del Estudiante</h1>
        <p class="subtitulo-encabezado">Información completa de <?php echo $estudiante['nombreEstudiante']; ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/estudiantes/verEstudiantes.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
        <a href="vistas/estudiantes/modificarEstudiantes.php?idEstudiante=<?php echo $idDelEstudiante; ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Estudiante
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><i class="fas fa-times-circle"></i> <?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user color-primary mr-10"></i> Información Personal</h3>
    </div>
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">ID Sistema</label>
            <p class="texto-negrita"><?php echo $idDelEstudiante; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Nombre Completo</label>
            <p class="texto-negrita"><?php echo $estudiante['nombreEstudiante']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Documento Identidad (DNI)</label>
            <p class="texto-negrita"><?php echo $estudiante['dniEstudiante']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Correo Electrónico</label>
            <p class="texto-negrita"><?php echo $estudiante['emailEstudiante']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Teléfono de Contacto</label>
            <p class="texto-negrita"><?php if (isset($estudiante['telefonoEstudiante'])) { echo $estudiante['telefonoEstudiante']; } else { echo '-'; } ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha de Nacimiento</label>
            <p class="texto-negrita"><?php echo $estudiante['fechaNacimientoEstudiante']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Dirección</label>
            <p class="texto-negrita"><?php if (isset($estudiante['direccionEstudiante'])) { echo $estudiante['direccionEstudiante']; } else { echo '-'; } ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Ciudad y CP</label>
            <p class="texto-negrita"><?php if (isset($estudiante['ciudadEstudiante'])) { echo $estudiante['ciudadEstudiante']; } else { echo '-' . " (" . $estudiante['codigoPostalEstudiante'] ?? '-' . ")"; } ?></p>
        </div>
    </div>
</div>

<!-- NUEVA SECCIÓN: TFG -->
<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-file-pdf color-danger mr-10"></i> Trabajo Fin de Grado (TFG)</h3>
    </div>
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Archivo del TFG</label>
            <div class="mt-5">
                <?php if (!empty($estudiante['archivoTFG'])) { ?>
                    <a href="uploads/tfg/<?php echo $estudiante['archivoTFG']; ?>" target="_blank" class="boton-secundario">
                        <i class="fas fa-download"></i> Descargar TFG (PDF)
                    </a>
                    <p class="texto-pequeno texto-atenuado mt-5">Archivo: <?php echo $estudiante['archivoTFG']; ?></p>
                <?php } else { ?>
                    <p class="texto-atenuado">No se ha subido ningún archivo todavía.</p>
                <?php } ?>
            </div>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Subir / Cambiar TFG (Solo PDF)</label>
            <form action="controladores/estudiantes/subirTFG.php" method="POST" enctype="multipart/form-data" class="disposicion-flexible alinear-centro separacion-pequena mt-5">
                <input type="hidden" name="idEstudiante" value="<?php echo $idDelEstudiante; ?>">
                <input type="file" name="archivoTFG" accept=".pdf" required>
                <button type="submit" name="subirTFG" class="boton-primario">
                    <i class="fas fa-upload"></i> Subir
                </button>
            </form>
        </div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-graduation-cap color-success mr-10"></i> Información Académica</h3>
    </div>
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Ciclo Formativo Matriculado</label>
            <?php 
            $datosCiclo = obtenerCicloUnico($estudiante['idCiclo']);
            ?>
            <p class="texto-negrita"><?php if (isset($datosCiclo['nombreCiclo'])) { echo $datosCiclo['nombreCiclo']; } else { echo 'Sin asignar'; } ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha de Alta</label>
            <p class="texto-negrita"><?php if (isset($estudiante['fechaAltaEstudiante'])) { echo $estudiante['fechaAltaEstudiante']; } else { echo '-'; } ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Estado del Alumno</label>
            <div>
                <?php 
                $claseEstado = ($estudiante['idEstado'] == 1) ? 'activo-verde' : 'inactivo-rojo';
                $textoEstado = ($estudiante['idEstado'] == 1) ? 'Activo' : 'Inactivo';
                ?>
                <span class="estado-bolita <?php echo $claseEstado; ?>">
                    <?php echo $textoEstado; ?>
                </span>
            </div>
        </div>
        <div class="campo-formulario campo-ancho-total">
            <label class="texto-atenuado texto-pequeno">Observaciones Internas</label>
            <div class="tarjeta-gris-suave">
                <?php if (!empty($estudiante['observacionesEstudiante'])) { echo nl2br($estudiante['observacionesEstudiante']); } else { echo 'Sin observaciones registradas.'; } ?>
            </div>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
