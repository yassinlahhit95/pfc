<?php
session_start();

$tituloDelPagina = "Nuevo Mensaje - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";

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
                <input type="text" name="asunto" placeholder="Duda sobre contenido, problema técnico...">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Mensaje *</label>
                <textarea name="descripcion" rows="5" placeholder="Escribe aquí tu mensaje..."></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="enviarMensaje" class="boton-primario">Enviar Mensaje</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
