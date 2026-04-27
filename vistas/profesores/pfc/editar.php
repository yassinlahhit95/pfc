<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$id = $_GET['id'];
$tfg = obtenerTFGporEstudiante($id);

$tituloDelPagina = "Editar TFG - Portal Profesores";
$seccionActual = 'tfg';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Datos TFG</h1>
    <a href="/pfc/vistas/profesores/pfc/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/profesores/pfc/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?php echo $id; ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante</label>
                <input type="text" value="<?php echo $tfg['nombreEstudiante']; ?>" disabled>
            </div>

            <div class="campo-formulario">
                <label>Título del TFG *</label>
                <input type="text" name="tituloTFG" value="<?php echo $tfg['tituloTFG']; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarTFG" class="boton-primario">Actualizar TFG</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

