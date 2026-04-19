<?php
session_start();
$titulo_pagina = "Agregar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../modelos/conectar.php";
require_once "../../modelos/niveles.php";
require_once "../../modelos/profesores.php";
require_once "../../modelos/aulas.php";

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();
$listaAulas = listarAulas();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_ciclo'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_ciclo']);

// Variables simples (Estudiante way)
$nombre = $datos['nombreCiclo'] ?? '';
$idNivelElegido = $datos['idNivel'] ?? '';
$idEstadoElegido = $datos['idEstado'] ?? 1;
$profesoresElegidos = $datos['profesores'] ?? [];
$aulasElegidas = $datos['aulas'] ?? [];
$descripcion = $datos['descripcionCiclo'] ?? '';
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
                <input type="text" id="nombreCiclo" name="nombreCiclo" value="<?php echo $nombre; ?>" placeholder="Ej: Desarrollo de Aplicaciones Web">
                <?php if (isset($errores['nombreCiclo'])) { ?>
                    <span class="error-campo"><?php echo $errores['nombreCiclo']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idNivel">Nivel Educativo *</label>
                <select id="idNivel" name="idNivel">
                    <option value="">-- Seleccionar Nivel --</option>
                    <?php foreach($listaNiveles as $nivel) { ?>
                        <option value="<?php echo $nivel['idNivel']; ?>" <?php if ($idNivelElegido == $nivel['idNivel']) { echo 'selected'; } ?>>
                            <?php echo $nivel['nombreNivel']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idNivel'])) { ?>
                    <span class="error-campo"><?php echo $errores['idNivel']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idEstado">Estado Inicial *</label>
                <select id="idEstado" name="idEstado">
                    <option value="1" <?php if ($idEstadoElegido == 1) { echo 'selected'; } ?>>Activo</option>
                    <option value="2" <?php if ($idEstadoElegido == 2) { echo 'selected'; } ?>>Inactivo</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Profesores Tutores *</label>
                <div class="tarjeta-gris-suave scroll-vertical">
                    <?php foreach($listaProfesores as $profesor) { ?>
                        <label class="item-seleccionable">
                            <input type="checkbox" name="profesores[]" value="<?php echo $profesor['idProfesor']; ?>" <?php if (in_array($profesor['idProfesor'], $profesoresElegidos)) { echo 'checked'; } ?>>
                            <span class="texto-pequeno"><?php echo $profesor['nombreProfesor']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div class="campo-formulario">
                <label>Aulas Asignadas *</label>
                <div class="tarjeta-gris-suave scroll-vertical">
                    <?php foreach($listaAulas as $aula) { ?>
                        <label class="item-seleccionable">
                            <input type="checkbox" name="aulas[]" value="<?php echo $aula['idAula']; ?>" <?php if (in_array($aula['idAula'], $aulasElegidas)) { echo 'checked'; } ?>>
                            <span class="texto-pequeno"><?php echo $aula['nombreAula']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="descripcionCiclo">Descripción del Ciclo *</label>
                <textarea id="descripcionCiclo" name="descripcionCiclo" rows="4" placeholder="Escribe una breve descripción del ciclo..."><?php echo $descripcion; ?></textarea>
                <?php if (isset($errores['descripcionCiclo'])) { ?>
                    <span class="error-campo"><?php echo $errores['descripcionCiclo']; ?></span>
                <?php } ?>
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
