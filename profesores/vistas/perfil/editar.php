<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

$id = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($id);

$nombre = $profesor['nombreProfesor'];
$email = $profesor['emailProfesor'];
$telefono = $profesor['telefonoProfesor'];

$tituloDelPagina = "Editar Mi Perfil - Portal Profesores";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Editar Mi Perfil</h1>
    <a href="/pfc/profesores/vistas/perfil/ver.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../controladores/perfil/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php echo $nombre; ?>">
            </div>

            <div class="campo-formulario">
                <label>Correo Electrónico *</label>
                <input type="email" name="emailProfesor" value="<?php echo $email; ?>">
            </div>

            <div class="campo-formulario">
                <label>Número de Teléfono</label>
                <input type="tel" name="telefonoProfesor" value="<?php echo $telefono; ?>">
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