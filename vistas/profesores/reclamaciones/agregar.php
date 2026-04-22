<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idProfesor = $_SESSION['idProfesor'];
$estudiantes = listarEstudiantes();

$tituloDelPagina = "Nueva Reclamación - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Reportar Incidencia de Estudiante</h1>
    <a href="/pfc/vistas/profesores/reclamaciones/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/profesores/reclamaciones/insertar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $idProfesor; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($estudiantes as $est) { ?>
                        <option value="<?php echo $est['idEstudiante']; ?>"><?php echo $est['nombreEstudiante']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Asunto *</label>
                <input type="text" name="asunto" placeholder="Falta de respeto, No entrega de tareas...">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción detallada *</label>
                <textarea name="descripcion" rows="5" placeholder="Explica aquí lo sucedido..."></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="insertarReclamacion" class="boton-primario">Enviar Reporte</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
