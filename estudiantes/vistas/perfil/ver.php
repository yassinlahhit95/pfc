<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id);

$tituloDelPagina = "Mi Perfil - Portal Estudiantes";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";

$nombre = $estudiante['nombreEstudiante'];
$email = $estudiante['emailEstudiante'];
$telefono = $estudiante['telefonoEstudiante'];
$dni = $estudiante['dniEstudiante'];
$fechaNacimiento = $estudiante['fechaNacimientoEstudiante'];
$direccion = $estudiante['direccionEstudiante'];
$ciudad = $estudiante['ciudadEstudiante'];
$codigoPostal = $estudiante['codigoPostalEstudiante'];
?>

<div class="encabezado-pagina">
    <h1>Mi Perfil</h1>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Información Personal</h3>
    </div>
    
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Nombre Completo</label>
            <p class="texto-negrita"><?php echo $nombre; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Número de Documento (DNI)</label>
            <p class="texto-negrita"><?php echo $dni; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Correo Electrónico</label>
            <p class="texto-negrita"><?php echo $email; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Número de Teléfono</label>
            <p class="texto-negrita"><?php echo $telefono; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha de Nacimiento</label>
            <p class="texto-negrita"><?php echo $fechaNacimiento; ?></p>
        </div>

        <div class="campo-formulario campo-ancho-completo">
            <label class="texto-atenuado texto-pequeno">Dirección Física</label>
            <p class="texto-negrita"><?php echo $direccion; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Ciudad</label>
            <p class="texto-negrita"><?php echo $ciudad; ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Código Postal</label>
            <p class="texto-negrita"><?php echo $codigoPostal; ?></p>
        </div>
    </div>

    <div class="margen-arriba">
        <a href="vistas/perfil/editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Perfil
        </a>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>