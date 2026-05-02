<?php
session_start();
$titulo_pagina = "Gestión de Directores - Super Admin";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/directores.php";

$todos_los_directores = listarDirectores();

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Directores de Ciclo</h1>
    <a href="/pfc/vistas/admin/directores/agregarDirectores.php" class="boton-primario">
        <i class="fas fa-plus"></i> Nuevo Director
    </a>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
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
                    <tr><td colspan="5" class="sin-datos">No hay directores registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_directores as $director) { ?>
                    <tr>
                        <td><?php echo $director['idDirector']; ?></td>
                        <td><strong><?php echo $director['nombreDirector']; ?></strong></td>
                        <td><?php echo $director['emailDirector']; ?></td>
                        <td><?php echo $director['telefonoDirector']; ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/directores/verDetallesDirectores.php?id=<?php echo $director['idDirector']; ?>" class="btn-accion btn-ver" title="Ver ficha completa">
                                    <i class="fas fa-search"></i>
                                </a>
                                <a href="/pfc/vistas/admin/directores/modificarDirectores.php?idDirector=<?php echo $director['idDirector']; ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/directores/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este director?')">
                                    <input type="hidden" name="idDirector" value="<?php echo $director['idDirector']; ?>">
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

