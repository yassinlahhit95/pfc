<?php
session_start();
$titulo_pagina = "AULAPRO | GESTIÓN DE DIRECTORES";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/directores.php";

$todos_los_directores = listarDirectores();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="cabecera">
    <h1>DIRECTORES DE CICLO</h1>
    <a href="agregarDirectores.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO DIRECTOR
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <div class="tcont">
        <table class="tabla-datos" id="tablaDirectores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_directores)) { ?>
                    <tr><td colspan="5" class="vacio">No hay directores registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_directores as $director) { ?>
                    <tr>
                        <td><?= $director['idDirector'] ?></td>
                        <td><b><?= $director['nombreDirector'] ?></b></td>
                        <td><?= $director['emailDirector'] ?></td>
                        <td><?= $director['telefonoDirector'] ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="verDetallesDirectores.php?id=<?= $director['idDirector'] ?>" class="btn-accion btn-ver" title="Ver ficha completa">
                                    <i class="fas fa-search"></i>
                                </a>
                                <a href="modificarDirectores.php?idDirector=<?= $director['idDirector'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/directores/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar este director?')">
                                    <input type="hidden" name="idDirector" value="<?= $director['idDirector'] ?>">
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




