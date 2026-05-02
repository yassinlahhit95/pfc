<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

require_once __DIR__ . "/../../../modelos/profesores.php";

$id = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($id);

$nombre = $profesor['nombreProfesor'];
$email = $profesor['emailProfesor'];
$telefono = $profesor['telefonoProfesor'];

$tituloDelPagina = "Editar Mi Perfil - Portal Profesores";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Mi Perfil</h1>
    <a href="ver.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/perfil/actualizar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idProfesor" value="<?= $id ?>">

        <div class="titulo-tarjeta"><h3>Datos de Contacto</h3></div>

        <div class="campo-formulario">
            <label>Nombre Completo</label>
            <input type="text" name="nombreProfesor" value="<?= $nombre ?>">
        </div>

        <div class="campo-formulario">
            <label>Correo Corporativo</label>
            <input type="text" name="emailProfesor" value="<?= $email ?>">
        </div>

        <div class="campo-formulario">
            <label>Número de Teléfono</label>
            <input type="tel" name="telefonoProfesor" value="<?= $telefono ?>">
        </div>

        <div class="titulo-tarjeta mt-30"><h3>Seguridad y Contraseña</h3></div>
        <p class="texto-atenuado mb-15">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo-formulario">
            <label>Contraseña Actual</label>
            <input type="password" name="current_password" placeholder="Escriba su contraseña actual para validar">
        </div>

        <div class="campo-formulario">
            <label>Nueva Contraseña</label>
            <input type="password" name="new_password" placeholder="Mínimo 6 caracteres">
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarPerfil" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <a href="ver.php" class="boton-secundario ml-10">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


