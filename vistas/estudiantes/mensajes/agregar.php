<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);

// Obtenemos los profesores asignados con sus módulos
$listaDeProfesores = obtenerProfesoresConModulosParaEstudiante($idEstudiante);

$tituloDelPagina = "Nuevo Mensaje - Portal Estudiantes";
$seccionActual = 'reclamaciones'; // Mantenemos el nombre de la sección para el CSS activo por ahora
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Mensaje</h1>
    <a href="/pfc/vistas/estudiantes/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/estudiantes/mensajes/insertar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?php echo $idEstudiante; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Destinatario (Profesor o Dirección)</label>
                <select name="idProfesor">
                    <option value="">-- Dirección (Administración) --</option>
                    <?php foreach ($listaDeProfesores as $profesor) { ?>
                        <option value="<?php echo $profesor['idProfesor']; ?>">
                            <?php echo $profesor['nombreProfesor'] . " (" . $profesor['nombreModulo'] . ")"; ?>
                        </option>
                    <?php } ?>
                </select>
                <small>Selecciona a quién quieres dirigir tu consulta.</small>
            </div>

            <div class="campo-formulario">
                <label>Asunto *</label>
                <input type="text" name="asunto" placeholder="Duda sobre contenido, problema técnico..." required>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Mensaje *</label>
                <textarea name="descripcion" rows="5" placeholder="Escribe aquí tu mensaje..." required></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="enviarMensaje" class="boton-primario">Enviar Mensaje</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
