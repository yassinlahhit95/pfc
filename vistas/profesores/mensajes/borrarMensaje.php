<?php
session_start();
if (empty($_SESSION['idProfesor'])) { header("Location: ../../login.php"); exit; }
$id = $_GET['id'] ?? '';
require_once __DIR__ . '/../../../modelos/reclamaciones.php';
$registro = obtenerMensajePorId($id);
$tituloDelPagina = 'AULAPRO | CONFIRMAR';
$seccionActual = '';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>CONFIRMAR ELIMINACIÓN</h1>
</div>

<div class="panel" style="max-width:500px;">
    <p>Quieres eliminar el mensaje "<?= $registro['asunto'] ?>"!</p>
    <div class="acciones" style="margin-top:20px;">
        <form method="POST" action="../../../controladores/profesores/mensajes/borrar.php">
            <input type="hidden" name="idReclamacion" value="<?= $id ?>">
            <button type="submit" class="boton-primario" style="background:#f87171;border-color:#f87171;min-width:160px;">Sí, eliminar</button>
        </form>
        <a href="lista.php" class="boton-secundario" style="min-width:160px;">Cancelar</a>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
