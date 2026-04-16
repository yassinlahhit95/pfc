<?php
session_start();
require_once "../../modelos/conectar.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/ciclos.php";

$idEstudiante = $_GET['id'] ?? 0;
$estudianteObj = new estudiante();
$estudiante = $estudianteObj->obtenerEstudiantePorIdModelo($idEstudiante);

if (!$estudiante) {
    $_SESSION['error'] = "Estudiante no encontrado";
    header("Location: verEstudiantes.php");
    exit;
}

$titulo_pagina = "Detalles Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Detalles del Estudiante</h1>
        <p class="subtitulo-encabezado">Información completa de <?php echo htmlspecialchars($estudiante['nombreEstudiante']); ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/estudiantes/verEstudiantes.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
        <a href="vistas/estudiantes/modificarEstudiantes.php?id=<?php echo $idEstudiante; ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Estudiante
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user color-primary mr-10"></i> Información Personal</h3>
    </div>
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">ID Sistema</label>
            <p class="texto-negrita"><?php echo $idEstudiante; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Nombre Completo</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['nombreEstudiante']); ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Documento Identidad (DNI)</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['dniEstudiante']); ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Correo Electrónico</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['emailEstudiante']); ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Teléfono de Contacto</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['telefonoEstudiante'] ?? '-'); ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha de Nacimiento</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['fechaNacimientoEstudiante']); ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Dirección</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['direccionEstudiante'] ?? '-'); ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Ciudad y CP</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['ciudadEstudiante'] ?? '-') . " (" . htmlspecialchars($estudiante['codigoPostalEstudiante'] ?? '-') . ")"; ?></p>
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
            $cicloObj = new ciclo();
            $datosCiclo = $cicloObj->obtenerCicloPorIdModelo($estudiante['idCiclo']);
            ?>
            <p class="texto-negrita"><?php echo htmlspecialchars($datosCiclo['nombreCiclo'] ?? 'Sin asignar'); ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha de Alta</label>
            <p class="texto-negrita"><?php echo htmlspecialchars($estudiante['fechaAltaEstudiante'] ?? '-'); ?></p>
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
            <p class="tarjeta-blanca sin-margen" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <?php echo nl2br(htmlspecialchars($estudiante['observacionesEstudiante'] ?? 'Sin observaciones registradas.')); ?>
            </p>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
