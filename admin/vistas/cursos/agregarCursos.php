<?php
session_start();
$titulo_pagina = "Agregar Curso - Super Admin";
$seccion = 'cursos';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/niveles.php";
require_once "../../modelos/profesores.php";
require_once "../../modelos/aulas.php";
require_once "../../modelos/ciclos.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();

$modeloNivel = new nivel($conexionBD);
$modeloProfesor = new profesor($conexionBD);
$modeloAula = new aula($conexionBD);
$modeloCiclo = new ciclo($conexionBD);

$listaNiveles = $modeloNivel->listarNivelesModelo();
$listaProfesores = $modeloProfesor->listarProfesoresModelo();
$listaAulas = $modeloAula->listarAulasModelo();
$listaEstados = $modeloAula->listarEstadosModelo();
$listaCiclos = $modeloCiclo->listarCiclosModelo();

// Errores y persistencia
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_curso'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_curso']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Curso Académico</h1>
    <a href="vistas/cursos/verCursos.php" class="boton-gris">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/cursosControlador.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Nombre del Curso *</label>
                <input type="text" name="nombreCurso" 
                       value="<?php echo htmlspecialchars($datos['nombreCurso'] ?? ''); ?>" placeholder="Ej: 1º Bachillerato A">
                <?php if (isset($errores['nombreCurso'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nombreCurso']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Nivel Académico *</label>
                <select name="idNivel">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach($listaNiveles as $n) { 
                        $selected = ($datos['idNivel'] ?? '') == $n['idNivel'] ? 'selected' : '';
                        echo "<option value='{$n['idNivel']}' {$selected}>{$n['nombreNivel']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idNivel'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idNivel']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Profesor Tutor *</label>
                <select name="idProfesor">
                    <option value="">-- Seleccionar Tutor --</option>
                    <?php foreach($listaProfesores as $p) { 
                        $selected = ($datos['idProfesor'] ?? '') == $p['idProfesor'] ? 'selected' : '';
                        echo "<option value='{$p['idProfesor']}' {$selected}>{$p['nombreProfesor']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Aula Asignada *</label>
                <select name="idAula">
                    <option value="">-- Seleccionar Aula --</option>
                    <?php foreach($listaAulas as $a) { 
                        $selected = ($datos['idAula'] ?? '') == $a['idAula'] ? 'selected' : '';
                        echo "<option value='{$a['idAula']}' {$selected}>{$a['nombreAula']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idAula'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idAula']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Estado del Curso *</label>
                <select name="idEstado">
                    <option value="">-- Seleccionar Estado --</option>
                    <?php foreach($listaEstados as $e) { 
                        $selected = ($datos['idEstado'] ?? '') == $e['idEstado'] ? 'selected' : '';
                        echo "<option value='{$e['idEstado']}' {$selected}>{$e['nombreEstado']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idEstado'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idEstado']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo Académico *</label>
                <select name="idCiclo">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach($listaCiclos as $c) { 
                        $selected = ($datos['idCiclo'] ?? '') == $c['idCiclo'] ? 'selected' : '';
                        echo "<option value='{$c['idCiclo']}' {$selected}>{$c['nombreCiclo']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idCiclo'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idCiclo']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción / Observaciones *</label>
                <textarea name="descripcionCurso" rows="3"><?php echo htmlspecialchars($datos['descripcionCurso'] ?? ''); ?></textarea>
                <?php if (isset($errores['descripcionCurso'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['descripcionCurso']; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarCurso" class="boton-azul">Crear Curso</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
