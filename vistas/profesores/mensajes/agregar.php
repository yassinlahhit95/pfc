<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesor = $_SESSION['idProfesor'];

// Obtenemos los ciclos del profesor para filtrar
$listaDeCiclos = obtenerCiclosDeProfesor($idProfesor);

// Filtro de ciclo
$idCicloSeleccionado = $_GET['idCiclo'] ?? "";

if (!empty($idCicloSeleccionado)) {
    $listaDeEstudiantes = listarEstudiantesPorCiclo($idCicloSeleccionado);
} else {
    $listaDeEstudiantes = listarEstudiantesPorProfesor($idProfesor);
}

$tituloDelPagina = "Nuevo Mensaje - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Redactar Mensaje</h1>
    <a href="/pfc/vistas/profesores/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    
    <!-- Filtro de Ciclo -->
    <form method="GET" class="margen-abajo disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario">
            <label>Filtrar Estudiantes por Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos mis alumnos --</option>
                <?php foreach ($listaDeCiclos as $ciclo) { ?>
                    <option value="<?php echo $ciclo['idCiclo']; ?>" <?php echo ($idCicloSeleccionado == $ciclo['idCiclo'] ? 'selected' : ''); ?>>
                        <?php echo $ciclo['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>

    <hr class="margen-abajo">

    <form action="/pfc/controladores/profesores/mensajes/insertar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $idProfesor; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Destinatario (Estudiante o Dirección)</label>
                <select name="idEstudiante" required>
                    <option value="">-- Seleccionar Destinatario --</option>
                    <option value="1">Dirección (Administración)</option>
                    <optgroup label="Estudiantes">
                        <?php foreach ($listaDeEstudiantes as $estudiante) { ?>
                            <option value="<?php echo $estudiante['idEstudiante']; ?>">
                                <?php echo $estudiante['nombreEstudiante']; ?> (<?php echo $estudiante['nombreCiclo']; ?>)
                            </option>
                        <?php } ?>
                    </optgroup>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Asunto *</label>
                <input type="text" name="asunto" placeholder="Motivo del mensaje..." required>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Mensaje *</label>
                <textarea name="descripcion" rows="5" placeholder="Escribe aquí tu explicación o consulta..." required></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="enviarMensaje" class="boton-primario">Enviar Mensaje</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
