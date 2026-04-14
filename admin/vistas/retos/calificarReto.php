<?php
session_start();
$titulo_pagina = "Calificar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/retos.php";
require_once "../../modelos/estudiantes.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: verRetos.php");
    exit;
}

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();
$retoObj = new reto($conexion);
$retoActual = $retoObj->obtenerRetoPorIdModelo($id);

if (!$retoActual) {
    header("Location: verRetos.php");
    exit;
}

$estudianteObj = new estudiante($conexion);
$listaEstudiantes = $estudianteObj->listarEstudiantesModelo();

$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Calificar Reto</h1>
        <p class="subtitulo-encabezado">Reto: <?php echo htmlspecialchars($retoActual['nombreReto']); ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/verRetos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if ($exito): ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo htmlspecialchars($exito); ?></p>
</div>
<?php endif; ?>

<div class="contenedor-tabla">
    <form action="controlador/retosControlador.php" method="POST">
        <input type="hidden" name="accion" value="calificar">
        <input type="hidden" name="idReto" value="<?php echo $retoActual['idReto']; ?>">
        
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>DNI</th>
                    <th>Curso</th>
                    <th>Nota (0-10)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEstudiantes)): ?>
                <tr>
                    <td colspan="4" class="sin-datos">No hay estudiantes registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($listaEstudiantes as $e) { 
                        $notaActual = $retoObj->obtenerCalificacion($e['idEstudiante'], $id);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($e['nombreEstudiante']); ?></td>
                        <td><?php echo htmlspecialchars($e['dniEstudiante']); ?></td>
                        <td><?php echo htmlspecialchars($e['nombreCurso'] ?? 'Sin curso'); ?></td>
                        <td>
                            <input type="number" name="notas[<?php echo $e['idEstudiante']; ?>]" 
                                   step="0.1" min="0" max="10" 
                                   value="<?php echo $notaActual; ?>"
                                   style="padding: 5px; width: 80px; border-radius: 4px; border: 1px solid #ddd;">
                        </td>
                    </tr>
                    <?php } ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="botones-formulario" style="margin-top: 20px;">
            <button type="submit" class="boton-primario">Guardar Calificaciones</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
