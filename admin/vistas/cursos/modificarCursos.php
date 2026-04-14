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

$queryCiclos = $db->query("SELECT idCiclo, nombreCiclo FROM ciclos ORDER BY nombreCiclo ASC");
$ciclos = [];
while($row = $queryCiclos->fetch_assoc()) { $ciclos[] = $row; }

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
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
                <label>Nombre del Curso *</label>
                <input type="text" name="nombreCurso" value="<?php echo htmlspecialchars($curso['nombreCurso']); ?>">
                <?php if (isset($errores['nombreCurso'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nombreCurso']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Nivel *</label>
                <select name="idNivel">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach($niveles as $n) { ?>
                        <option value="<?php echo $n['idNivel']; ?>" <?php if($curso['idNivel'] == $n['idNivel']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($n['nombreNivel']); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idNivel'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idNivel']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Profesor Tutor *</label>
                <select name="idProfesor">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach($profesores as $p) { ?>
                        <option value="<?php echo $p['idProfesor']; ?>" <?php if($curso['idProfesor'] == $p['idProfesor']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($p['nombreProfesor']); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Aula *</label>
                <select name="idAula">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach($aulas as $a) { ?>
                        <option value="<?php echo $a['idAula']; ?>" <?php if($curso['idAula'] == $a['idAula']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($a['nombreAula']); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idAula'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idAula']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Estado *</label>
                <select name="idEstado">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach($estados as $e) { ?>
                        <option value="<?php echo $e['idEstado']; ?>" <?php if($curso['idEstado'] == $e['idEstado']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($e['nombreEstado']); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idEstado'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idEstado']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Ciclo *</label>
                <select name="idCiclo">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach($ciclos as $c) { ?>
                        <option value="<?php echo $c['idCiclo']; ?>" <?php if($curso['idCiclo'] == $c['idCiclo']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['nombreCiclo']); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCiclo'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idCiclo']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Descripción *</label>
                <textarea name="descripcionCurso" rows="3"><?php echo htmlspecialchars($curso['descripcionCurso']); ?></textarea>
                <?php if (isset($errores['descripcionCurso'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['descripcionCurso']; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="acciones-formulario">
            <a href="vistas/cursos/verCursos.php" class="boton-cancelar">Cancelar</a>
            <button type="submit" name="guardarCurso" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
