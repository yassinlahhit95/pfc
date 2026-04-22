<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$id = $_GET['id'];


function obtenerReclamacionPorId($id) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM reclamaciones WHERE idReclamacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

$rec = obtenerReclamacionPorId($id);

$tituloDelPagina = "Editar Reclamación - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Atender Reclamación</h1>
    <a href="/pfc/vistas/profesores/reclamaciones/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/profesores/reclamaciones/actualizar.php" method="POST">
        <input type="hidden" name="idReclamacion" value="<?php echo $id; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Asunto</label>
                <input type="text" value="<?php echo $rec['asunto']; ?>" disabled>
            </div>

            <div class="campo-formulario">
                <label>Estado *</label>
                <select name="estadoReclamacion">
                    <option value="pendiente" <?php if ($rec['estadoReclamacion'] == 'pendiente') { echo 'selected'; } ?>>Pendiente</option>
                    <option value="atendido" <?php if ($rec['estadoReclamacion'] == 'atendido') { echo 'selected'; } ?>>Atendido</option>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-completo">
                <label>Descripción</label>
                <textarea disabled rows="4"><?php echo $rec['descripcion']; ?></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarReclamacion" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
