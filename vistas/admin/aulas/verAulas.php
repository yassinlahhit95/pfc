<?php
session_start();
$titulo_pagina = "Gestión de Aulas - Super Admin";
$seccion = 'aulas';
include_once "../comunes/nav.php";

require_once "../../../modelos/aulas.php";

$listaAulas = listarAulas();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_aulas'])) {
    $datos = $_SESSION['datos_aulas'];
}
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Aulas del Centro</h1>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<!-- Formulario Simple (en fila) -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nueva Aula</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/aulas/insertar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Nombre del Aula</label>
            <?php 
            $nombreAula = '';
            if (isset($datos['nombreAula'])) {
                $nombreAula = $datos['nombreAula'];
            }
            ?>
            <input type="text" name="nombreAula" value="<?php echo $nombreAula; ?>" placeholder="Ej: Aula 101">
            <?php if (isset($errores['nombreAula'])) { ?>
                <p class="error-campo"><?php echo $errores['nombreAula']; ?></p>
            <?php } ?>
        </div>
        <div class="mt-25">
            <button type="submit" name="guardarAula" class="boton-primario">
                <i class="fas fa-save"></i> Registrar
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaAulas)) { ?>
                    <tr><td colspan="3" class="sin-datos">No hay aulas configuradas</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaAulas as $aula) { ?>
                    <tr>
                        <td><?php echo $aula['idAula']; ?></td>
                        <td><strong><?php echo $aula['nombreAula']; ?></strong></td>
                        <td>
                            <form action="/pfc/controladores/admin/aulas/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Borrar aula?');">
                                <input type="hidden" name="idAula" value="<?php echo $aula['idAula']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>