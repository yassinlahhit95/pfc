<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

$id = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($id);

$tituloDelPagina = "Mi Perfil - Portal Profesores";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";

$nombre = $profesor['nombreProfesor'];
$email = $profesor['emailProfesor'];
$telefono = $profesor['telefonoProfesor'];
$dni = $profesor['dniProfesor'];
$especialidad = $profesor['especialidad'];
$direccion = $profesor['direccionProfesor'];
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

        <div class="campo-formulario campo-ancho-completo">
            <label class="texto-atenuado texto-pequeno">Especialidad Docente</label>
            <p class="texto-negrita"><?php echo $especialidad; ?></p>
        </div>

        <div class="campo-formulario campo-ancho-completo">
            <label class="texto-atenuado texto-pequeno">Dirección Física</label>
            <p class="texto-negrita"><?php echo $direccion; ?></p>
        </div>
    </div>

    <div class="margen-arriba">
        <a href="vistas/perfil/editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Perfil
        </a>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>