<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = $_GET['id'] ?? '';
$datosTFG = obtenerTFGporEstudiante($idEstudiante);

$tituloPagina = "Editar TFG - Portal Profesores";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Datos TFG</h1>
    <a href="../../../vistas/profesores/pfc/lista.php" class="boton-secundario">← VOLVER</a>
</div>

<?php if ($error): ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito): ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/pfc/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= htmlspecialchars($idEstudiante) ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante</label>
                <input type="text" value="<?= htmlspecialchars($datosTFG['nombreEstudiante'] ?? '') ?>" disabled>
            </div>

            <div class="campo-formulario">
                <label>Título del TFG *</label>
                <input type="text" name="tituloTFG" value="<?= htmlspecialchars($datosTFG['tituloTFG'] ?? '') ?>" class="<?= isset($errores['tituloTFG']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['tituloTFG'])): ?>
                    <strong class="error-campo"><?= $errores['tituloTFG'] ?></strong>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarTFG" class="boton-primario">
                <i class="fas fa-save"></i> ACTUALIZAR TFG
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>




