<?php
session_start();
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();
$listaAulas = listarAulas();

$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_ciclo'] ?? [];

unset($_SESSION['errores'], $_SESSION['datos_ciclo']);

$profesoresElegidos = $datos['profesores'] ?? [];
$aulasElegidas = $datos['aulas'] ?? [];

$titulo_pagina = "Agregar Ciclo - Admin";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Ciclo</h1>
        <p class="subtitulo-encabezado">Defina un nuevo programa formativo y asigne recursos</p>
    </div>
    <a href="verCiclos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER A LA LISTA
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/ciclos/insertar.php" method="POST">
        <div class="form-estandar">
            <div class="campo-formulario">
                <label>Nombre del Ciclo *</label>
                <input type="text" name="nombreCiclo" placeholder="Desarrollo de Aplicaciones Web" value="<?= $datos['nombreCiclo'] ?? '' ?>">
                <?php if (isset($lista_de_errores['nombreCiclo'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Abreviatura *</label>
                <input type="text" name="abreviaturaCiclo" placeholder="Ej: DAW, SMR, Bach..." maxlength="10" value="<?= $datos['abreviaturaCiclo'] ?? '' ?>">
                <?php if (isset($lista_de_errores['abreviaturaCiclo'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['abreviaturaCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Nivel Formativo *</label>
                <select name="idNivel">
                    <option value="">-- Seleccionar Nivel --</option>
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?= $nivel['idNivel'] ?>" <?php if (($datos['idNivel'] ?? '') == $nivel['idNivel']) { ?>selected<?php } ?>>
                            <?= $nivel['nombreNivel'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idNivel'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['idNivel'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Precio Total del Ciclo (€) *</label>
                <input type="number" name="precioCiclo" step="0.01" value="<?= $datos['precioCiclo'] ?? '1000.00' ?>">
            </div>
        </div>

        <div class="cuadricula-secundaria mt-25">
            <div>
                <h4 class="margen-abajo">Asignar Tutores/Profesores</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaProfesores as $prof) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="profesores[]" value="<?= $prof['idProfesor'] ?>"
                                <?php if (in_array($prof['idProfesor'], $profesoresElegidos)) { ?>checked<?php } ?>>
                            <span><?= $prof['nombreProfesor'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 class="margen-abajo">Asignar Aulas Habituales</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaAulas as $aula) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="aulas[]" value="<?= $aula['idAula'] ?>"
                                <?php if (in_array($aula['idAula'], $aulasElegidas)) { ?>checked<?php } ?>>
                            <span><?= $aula['nombreAula'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> CREAR CICLO FORMATIVO
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


