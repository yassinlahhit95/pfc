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

<div class="encabezado-pagina">
    <h1>Redactar Mensaje</h1>
    <a href="/pfc/vistas/profesores/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<?php if (isset($_SESSION['error'])) { ?>
    <div class="mensaje-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php } ?>
<?php if (isset($_SESSION['exito'])) { ?>
    <div class="mensaje-exito"><?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    
    <!-- Filtro de Ciclo -->
    <form method="GET" class="margen-abajo">
        <div class="disposicion-flexible alinear-fin separacion-grande">
            <div class="campo-formulario flexible-rellenar">
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
            <div>
                <a href="agregar.php" class="boton-secundario">LIMPIAR FILTRO</a>
            </div>
        </div>
    </form>

    <div class="titulo-tarjeta mt-30"><h3>Nuevo Mensaje</h3></div>

    <form action="/pfc/controladores/profesores/mensajes/insertar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idProfesor" value="<?php echo $idProfesor; ?>">
        
        <div class="campo-formulario">
            <label>Destinatario (Estudiante o Dirección)</label>
            <select name="idEstudiante">
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
            <label>Asunto del Mensaje</label>
            <input type="text" name="asunto" placeholder="Escriba el motivo del mensaje...">
        </div>

        <div class="campo-formulario">
            <label>Mensaje</label>
            <textarea name="descripcion" rows="6" placeholder="Escribe aquí tu mensaje (máximo 250 caracteres)..." maxlength="250"></textarea>
        </div>

        <div class="form-acciones">
            <button type="submit" name="enviarMensaje" class="boton-primario">
                <i class="fas fa-paper-plane"></i> ENVIAR MENSAJE
            </button>
            <a href="/pfc/vistas/profesores/mensajes/lista.php" class="boton-secundario ml-10">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
