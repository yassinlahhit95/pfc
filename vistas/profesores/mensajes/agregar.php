<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

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
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Redactar Mensaje</h1>
    <a href="lista.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
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
                        <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloSeleccionado == $ciclo['idCiclo'] ? 'selected' : '') ?>>
                            <?= $ciclo['nombreCiclo'] ?>
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

    <form action="../../../controladores/profesores/mensajes/insertar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idProfesor" value="<?= $idProfesor ?>">
        
        <div class="campo-formulario">
            <label>Destinatario (Estudiante o Dirección)</label>
            <select name="idEstudiante">
                <option value="">-- Seleccionar Destinatario --</option>
                <option value="1">Dirección (Administración)</option>
                <optgroup label="Estudiantes">
                    <?php foreach ($listaDeEstudiantes as $estudiante) { ?>
                        <option value="<?= $estudiante['idEstudiante'] ?>">
                            <?= $estudiante['nombreEstudiante'] ?> (<?= $estudiante['nombreCiclo'] ?>)
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
            <a href="lista.php" class="boton-secundario ml-10">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


