<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

$profesores = listarProfesores();
$idEstudiante = $_SESSION['idEstudiante'];

$tituloDelPagina = "Nueva Reclamación - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nueva Reclamación</h1>
    <a href="vistas/reclamaciones/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/reclamaciones/insertar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?php echo $idEstudiante; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Profesor *</label>
                <select name="idProfesor" required>
                    <?php foreach ($profesores as $prof) { ?>
                        <option value="<?php echo $prof['idProfesor']; ?>"><?php echo $prof['nombreProfesor']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Asunto *</label>
                <input type="text" name="asunto" required placeholder="Ej: Error en nota de examen">
            </div>

            <div class="campo-formulario">
                <label>Gravedad *</label>
                <select name="gravedad" required>
                    <option value="leve">Leve</option>
                    <option value="grave">Grave</option>
                    <option value="muy grave">Muy Grave</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Fecha *</label>
                <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="campo-formulario campo-ancho-completo">
                <label>Descripción detallada *</label>
                <textarea name="descripcion" rows="5" required placeholder="Explica aquí el motivo de tu reclamación..."></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="insertarReclamacion" class="boton-primario">Enviar Reclamación</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>