<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id);

$nombre = $estudiante['nombreEstudiante'];
$email = $estudiante['emailEstudiante'];
$telefono = $estudiante['telefonoEstudiante'];

$tituloDelPagina = "Editar Mi Perfil - Portal Estudiantes";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Editar Mi Perfil</h1>
    <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/estudiantes/perfil/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?php echo $nombre; ?>">
            </div>

            <div class="campo-formulario">
                <label>Correo Electrónico *</label>
                <input type="text" name="emailEstudiante" value="<?php echo $email; ?>">
            </div>

            <div class="campo-formulario">
                <label>Número de Teléfono</label>
                <input type="tel" name="telefonoEstudiante" value="<?php echo $telefono; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarPerfil" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
