<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
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
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Mi Perfil</h1>
    <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="boton-secundario">â† Volver</a>
</div>

<?php if (isset($_SESSION['error'])) { ?>
    <div class="mensaje-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/estudiantes/perfil/actualizar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idEstudiante" value="<?php echo $id; ?>">

        <div class="titulo-tarjeta"><h3>Datos Personales</h3></div>
        
        <div class="campo-formulario">
            <label>Nombre Completo</label>
            <input type="text" name="nombreEstudiante" value="<?php echo $nombre; ?>">
        </div>

        <div class="campo-formulario">
            <label>Correo ElectrÃ³nico</label>
            <input type="text" name="emailEstudiante" value="<?php echo $email; ?>">
        </div>

        <div class="campo-formulario">
            <label>NÃºmero de TelÃ©fono</label>
            <input type="tel" name="telefonoEstudiante" value="<?php echo $telefono; ?>">
        </div>

        <div class="titulo-tarjeta mt-30"><h3>Seguridad y ContraseÃ±a</h3></div>
        <p class="texto-atenuado mb-15">Rellene estos campos solo si desea cambiar su contraseÃ±a de acceso.</p>

        <div class="campo-formulario">
            <label>ContraseÃ±a Actual</label>
            <input type="password" name="current_password" placeholder="Escriba su contraseÃ±a para validar los cambios">
        </div>

        <div class="campo-formulario">
            <label>Nueva ContraseÃ±a</label>
            <input type="password" name="new_password" placeholder="Debe tener al menos 6 caracteres">
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarPerfil" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="boton-secundario ml-10">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

