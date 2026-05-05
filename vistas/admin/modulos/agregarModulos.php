<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

$titulo_pagina = "Registrar Módulo - Admin";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Módulo</h1>
    <a href="verModulos.php" class="boton-secundario">← VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/modulos/insertar.php" method="POST" class="form-estandar">

        <div class="campo-formulario">
            <label>Nombre del Módulo *</label>
            <input type="text" name="nombreModulo" value="<?= $datos['nombreModulo'] ?? '' ?>">
            <?php if (isset($lista_de_errores['nombreModulo'])) { ?>
                <strong class="error-campo"><?= $lista_de_errores['nombreModulo'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Ciclo Formativo Asociado *</label>
            <select name="idCiclo">
                <option value="">Seleccione un ciclo</option>
                <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= (isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                <strong class="error-campo"><?= $lista_de_errores['idCiclo'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Horas Máximas *</label>
            <input type="text" name="horasMaximas" value="<?= $datos['horasMaximas'] ?? '' ?>">
            <?php if (isset($lista_de_errores['horasMaximas'])) { ?>
                <strong class="error-campo"><?= $lista_de_errores['horasMaximas'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> REGISTRAR MÓDULO
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>



