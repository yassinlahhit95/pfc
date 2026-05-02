<?php
session_start();
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_modulo = $_GET['idModulo'] ?? 0;
$modulo = obtenerModuloPorId(intval($id_del_modulo));

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

if (!empty($datos)) {
    $modulo = array_merge($modulo, $datos);
}

$titulo_pagina = "Modificar Módulo - Admin";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Módulo: <?= $modulo['nombreModulo'] ?></h1>
    <a href="verModulos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?= $id_del_modulo ?>">

        <div class="form-estandar">
            <div class="campo-formulario">
                <label>Nombre del Módulo *</label>
                <input type="text" name="nombreModulo" value="<?= $modulo['nombreModulo'] ?>">
                <?php if (isset($lista_de_errores['nombreModulo'])) { ?>
                    <p class="texto-rojo texto-pequeno mt-5"><?= $lista_de_errores['nombreModulo'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo Formativo *</label>
                <select name="idCiclo">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?= ($modulo['idCiclo'] == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                    <p class="texto-rojo texto-pequeno mt-5"><?= $lista_de_errores['idCiclo'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasMaximas" value="<?= $modulo['horasMaximas'] ?>">
                <?php if (isset($lista_de_errores['horasMaximas'])) { ?>
                    <p class="texto-rojo texto-pequeno mt-5"><?= $lista_de_errores['horasMaximas'] ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="form-estandar-botones">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


