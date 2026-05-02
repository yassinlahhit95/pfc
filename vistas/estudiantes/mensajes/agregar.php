<?php
session_start();

$tituloDelPagina = "Nuevo Mensaje - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);

// Obtenemos los profesores asignados con sus módulos para el select
$listaDeProfesores = obtenerProfesoresConModulosParaEstudiante($idEstudiante);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Mensaje</h1>
    <a href="/pfc/vistas/estudiantes/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/estudiantes/mensajes/insertar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idEstudiante" value="<?php echo $idEstudiante; ?>">
        
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
            <small class="texto-atenuado">Selecciona a quién quieres dirigir tu consulta.</small>
        </div>

        <div class="campo-formulario">
            <label>Asunto del Mensaje</label>
            <input type="text" name="asunto" placeholder="Duda sobre contenido, problema técnico...">
        </div>

        <div class="campo-formulario">
            <label>Contenido del Mensaje</label>
            <textarea name="descripcion" rows="6" placeholder="Escribe aquí tu mensaje (máximo 250 caracteres)..." maxlength="250"></textarea>
        </div>

        <div class="form-acciones">
            <button type="submit" name="enviarMensaje" class="boton-primario">
                <i class="fas fa-paper-plane"></i> ENVIAR MENSAJE
            </button>
            <a href="/pfc/vistas/estudiantes/mensajes/lista.php" class="boton-secundario ml-10">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
