<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$idCiclo = $estudianteActual['idCiclo'];

$profesores = listarProfesoresPorCiclo($idCiclo);

$tituloDelPagina = "Nueva Reclamación - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nueva Reclamación</h1>
    <a href="/pfc/vistas/estudiantes/reclamaciones/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/estudiantes/reclamaciones/insertar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?php echo $idEstudiante; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Dirigida a (Profesor o Centro)</label>
                <select name="idProfesor">
                    <option value="">-- Asunto General / Otros --</option>
                    <?php foreach ($profesores as $prof) { ?>
                        <option value="<?php echo $prof['idProfesor']; ?>"><?php echo $prof['nombreProfesor']; ?></option>
                    <?php } ?>
                </select>
                <small>Selecciona un profesor o deja vacío para temas generales.</small>
            </div>

            <div class="campo-formulario">
                <label>Asunto *</label>
                <input type="text" name="asunto" placeholder="Error en nota, Instalaciones...">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción detallada *</label>
                <textarea name="descripcion" rows="5" placeholder="Explica aquí el motivo de tu reclamación..."></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="insertarReclamacion" class="boton-primario">Enviar Reclamación</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
