<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$id = $_GET['id'] ?? '';
$tfg = obtenerTFGporEstudiante($id);

$tituloDelPagina = "Editar TFG - Portal Profesores";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Datos TFG</h1>
    <a href="lista.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) : ?>
    <div class="alerta-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito) : ?>
    <div class="alerta-exito"><?= $exito ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/pfc/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $id ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante</label>
                <input type="text" value="<?= $tfg['nombreEstudiante'] ?>" disabled>
            </div>

            <div class="campo-formulario">
                <label>TÃ­tulo del TFG *</label>
                <input type="text" name="tituloTFG" value="<?= $tfg['tituloTFG'] ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarTFG" class="boton-primario">Actualizar TFG</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

