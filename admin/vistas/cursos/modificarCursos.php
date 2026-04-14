<?php
session_start();
$titulo_pagina = "Modificar Curso - Super Admin";
$seccion = 'cursos';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/cursos.php";

$id = $_GET['id'];
$conexionObj = new Conexion();
$db = $conexionObj->conectar();

$cursoObj = new curso($db);
$curso = $cursoObj->obtenerCursoPorIdModelo($id);

$queryNiveles = $db->query("SELECT idNivel, nombreNivel FROM niveles ORDER BY nombreNivel ASC");
$niveles = [];
while($row = $queryNiveles->fetch_assoc()) { $niveles[] = $row; }

$queryProfesores = $db->query("SELECT idProfesor, nombreProfesor FROM profesores ORDER BY nombreProfesor ASC");
$profesores = [];
while($row = $queryProfesores->fetch_assoc()) { $profesores[] = $row; }

$queryAulas = $db->query("SELECT idAula, nombreAula FROM aulas ORDER BY nombreAula ASC");
$aulas = [];
while($row = $queryAulas->fetch_assoc()) { $aulas[] = $row; }

$queryEstados = $db->query("SELECT idEstado, nombreEstado FROM estados ORDER BY nombreEstado ASC");
$estados = [];
while($row = $queryEstados->fetch_assoc()) { $estados[] = $row; }
?>

<div class="encabezado-pagina">
    <h1>Modificar Curso</h1>
    <p class="subtitulo-encabezado">Actualizando: <?php echo htmlspecialchars($curso['nombreCurso']); ?></p>
</div>

<div class="contenedor-formulario">
    <form method="POST" action="controlador/cursosControlador.php">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idCurso" value="<?php echo $curso['idCurso']; ?>">

        <div class="cuadricula-formulario">
            <div class="grupo-formulario ancho-completo">
                <label>Nombre del Curso</label>
                <input type="text" name="nombreCurso" value="<?php echo htmlspecialchars($curso['nombreCurso']); ?>" required />
            </div>

            <div class="grupo-formulario">
                <label>Nivel</label>
                <select name="idNivel" required>
                    <?php foreach($niveles as $n) { ?>
                        <option value="<?php echo $n['idNivel']; ?>" <?php if($curso['idNivel'] == $n['idNivel']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($n['nombreNivel']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="grupo-formulario">
                <label>Profesor Tutor</label>
                <select name="idProfesor">
                    <option value="">Sin Tutor</option>
                    <?php foreach($profesores as $p) { ?>
                        <option value="<?php echo $p['idProfesor']; ?>" <?php if($curso['idProfesor'] == $p['idProfesor']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($p['nombreProfesor']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="grupo-formulario">
                <label>Aula</label>
                <select name="idAula">
                    <?php foreach($aulas as $a) { ?>
                        <option value="<?php echo $a['idAula']; ?>" <?php if($curso['idAula'] == $a['idAula']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($a['nombreAula']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="grupo-formulario">
                <label>Estado</label>
                <select name="idEstado" required>
                    <?php foreach($estados as $e) { ?>
                        <option value="<?php echo $e['idEstado']; ?>" <?php if($curso['idEstado'] == $e['idEstado']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($e['nombreEstado']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Descripción</label>
                <textarea name="descripcionCurso" rows="3"><?php echo htmlspecialchars($curso['descripcionCurso']); ?></textarea>
            </div>
        </div>

        <div class="acciones-formulario">
            <a href="vistas/cursos/verCursos.php" class="boton-cancelar">Cancelar</a>
            <button type="submit" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
