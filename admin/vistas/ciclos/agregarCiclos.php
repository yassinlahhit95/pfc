<?php
session_start();
$titulo_pagina = "Agregar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../modelos/conectar.php";
require_once "../../modelos/niveles.php";
require_once "../../modelos/profesores.php";
require_once "../../modelos/aulas.php";

$modeloNivel = new nivel();
$modeloProfesor = new profesor();
$modeloAula = new aula();

$listaNiveles = $modeloNivel->listarNivelesModelo();
$listaProfesores = $modeloProfesor->listarProfesoresModelo();
$listaAulas = $modeloAula->listarAulasModelo();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_ciclo'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_ciclo']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Ciclo</h1>
        <p class="subtitulo-encabezado">Definir un nuevo ciclo formativo y sus recursos asociados</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/ciclos/verCiclos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-sync color-primary mr-10"></i> Configuración del Ciclo</h3>
    </div>
    <form action="controladores/ciclos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label for="nombreCiclo">Nombre del Ciclo *</label>
                <input type="text" id="nombreCiclo" name="nombreCiclo" 
                       placeholder="Ej: Desarrollo de Aplicaciones Web"
                       value="<?php echo htmlspecialchars($datos['nombreCiclo'] ?? ''); ?>"
                       class="<?php echo isset($errores['nombreCiclo']) ? 'input-error' : ''; ?>">
                <?php if (isset($errores['nombreCiclo'])): ?>
                    <span class="error-campo"><?php echo $errores['nombreCiclo']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label for="idNivel">Nivel Educativo *</label>
                <select id="idNivel" name="idNivel" class="<?php echo isset($errores['idNivel']) ? 'input-error' : ''; ?>">
                    <option value="">-- Seleccionar Nivel --</option>
                    <?php foreach($listaNiveles as $n) { 
                        $selected = ($datos['idNivel'] ?? '') == $n['idNivel'] ? 'selected' : '';
                        echo "<option value='{$n['idNivel']}' {$selected}>{$n['nombreNivel']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idNivel'])): ?>
                    <span class="error-campo"><?php echo $errores['idNivel']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label for="idEstado">Estado Inicial *</label>
                <select id="idEstado" name="idEstado">
                    <option value="1" <?php echo ($datos['idEstado'] ?? '') == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="2" <?php echo ($datos['idEstado'] ?? '') == 2 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
                <?php if (isset($errores['idEstado'])): ?>
                    <span class="error-campo"><?php echo $errores['idEstado']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Profesores Tutores * <span class="texto-atenuado">(Selecciona uno o más)</span></label>
                <div class="tarjeta-blanca sin-margen" style="background: #f8fafc; border: 1px solid #e2e8f0; max-height: 180px; overflow-y: auto; padding: 10px;">
                    <?php foreach($listaProfesores as $p) { 
                        $checked = (isset($datos['profesores']) && in_array($p['idProfesor'], $datos['profesores'])) ? 'checked' : '';
                    ?>
                        <label class="disposicion-flexible alinear-centro separacion-pequena cursor-pointer" style="padding: 5px 0;">
                            <input type="checkbox" name="profesores[]" value="<?php echo $p['idProfesor']; ?>" id="profe_<?php echo $p['idProfesor']; ?>" <?php echo $checked; ?>>
                            <span class="texto-pequeno"><?php echo htmlspecialchars($p['nombreProfesor']); ?></span>
                        </label>
                    <?php } ?>
                </div>
                <?php if (isset($errores['profesores'])): ?>
                    <span class="error-campo"><?php echo $errores['profesores']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Aulas Asignadas * <span class="texto-atenuado">(Selecciona una o más)</span></label>
                <div class="tarjeta-blanca sin-margen" style="background: #f8fafc; border: 1px solid #e2e8f0; max-height: 180px; overflow-y: auto; padding: 10px;">
                    <?php foreach($listaAulas as $a) { 
                        $checked = (isset($datos['aulas']) && in_array($a['idAula'], $datos['aulas'])) ? 'checked' : '';
                    ?>
                        <label class="disposicion-flexible alinear-centro separacion-pequena cursor-pointer" style="padding: 5px 0;">
                            <input type="checkbox" name="aulas[]" value="<?php echo $a['idAula']; ?>" id="aula_<?php echo $a['idAula']; ?>" <?php echo $checked; ?>>
                            <span class="texto-pequeno"><?php echo htmlspecialchars($a['nombreAula']); ?></span>
                        </label>
                    <?php } ?>
                </div>
                <?php if (isset($errores['aulas'])): ?>
                    <span class="error-campo"><?php echo $errores['aulas']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="descripcionCiclo">Descripción del Ciclo *</label>
                <textarea id="descripcionCiclo" name="descripcionCiclo" rows="4" 
                          placeholder="Escribe una breve descripción del ciclo..."
                          class="<?php echo isset($errores['descripcionCiclo']) ? 'input-error' : ''; ?>"><?php echo htmlspecialchars($datos['descripcionCiclo'] ?? ''); ?></textarea>
                <?php if (isset($errores['descripcionCiclo'])): ?>
                    <span class="error-campo"><?php echo $errores['descripcionCiclo']; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Ciclo Formativo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
