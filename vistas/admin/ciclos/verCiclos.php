<?php
session_start();
$titulo_pagina = "Gestión de Ciclos - Admin";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_ciclos'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_ciclos']);
?>

<div class="encabezado-pagina">
    <h1>Ciclos Formativos</h1>
    <a href="agregarCiclos.php" class="boton-primario">
        <i class="fas fa-plus"></i> Nuevo Ciclo
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaCiclos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Nivel</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_ciclos)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay ciclos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                    <tr>
                        <td><?= $ciclo['idCiclo'] ?></td>
                        <td><strong><?= $ciclo['nombreCiclo'] ?></strong></td>
                        <td><?= $ciclo['nombreNivel'] ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarCiclos.php?idCiclo=<?= $ciclo['idCiclo'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/ciclos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este ciclo?')">
                                    <input type="hidden" name="idCiclo" value="<?= $ciclo['idCiclo'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>


