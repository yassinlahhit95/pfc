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

<div class="encabezado-pagina">
    <h1>Editar Mi Perfil</h1>
    <a href="/pfc/vistas/profesores/perfil/ver.php" class="boton-secundario">← Volver</a>
</div>

<?php if (isset($_SESSION['error'])) { ?>
    <div class="mensaje-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/profesores/perfil/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php echo $nombre; ?>">
            </div>

            <div class="campo-formulario">
                <label>Correo Electrónico *</label>
                <input type="text" name="emailProfesor" value="<?php echo $email; ?>">
            </div>

            <div class="campo-formulario">
                <label>Número de Teléfono</label>
                <input type="tel" name="telefonoProfesor" value="<?php echo $telefono; ?>">
            </div>
        </div>

        <h3 class="margen-arriba mt-20"><i class="fas fa-lock"></i> SEGURIDAD (Opcional)</h3>
        <p class="texto-atenuado texto-pequeno">Solo rellene estos campos si desea cambiar su contraseña.</p>

        <div class="formulario-cuadricula mt-10">
            <div class="campo-formulario">
                <label>CONTRASEÑA ACTUAL:</label>
                <input type="password" name="current_password" placeholder="Para validar cambios">
            </div>

            <div class="campo-formulario">
                <label>NUEVA CONTRASEÑA:</label>
                <input type="password" name="new_password" placeholder="Mínimo 6 caracteres">
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

