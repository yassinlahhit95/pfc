<?php
session_start();
$titulo_pagina = "Gestión de Directores - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";

require_once "../../../modelos/directores.php";

$listaDirectores = listarDirectores();

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Directores</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/directores/agregarDirectores.php" class="boton-primario">
            <i class="fas fa-user-plus"></i> Nuevo Director
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Alta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDirectores)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay directores registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDirectores as $d) { ?>
                    <tr>
                        <td><?php echo $d['idDirector']; ?></td>
                        <td><strong><?php echo $d['nombreDirector']; ?></strong></td>
                        <td><?php echo $d['emailDirector']; ?></td>
                        <td><?php 
                            if (isset($d['telefonoDirector'])) {
                                echo $d['telefonoDirector'];
                            } else {
                                echo '-';
                            }
                        ?></td>
                        <td><?php echo date('d/m/Y', strtotime($d['fechaAltaDirector'])); ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/directores/verDetallesDirectores.php?id=<?php echo $d['idDirector']; ?>" 
                                   class="boton-icono boton-ver" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/pfc/vistas/admin/directores/modificarDirectores.php?id=<?php echo $d['idDirector']; ?>" 
                                   class="boton-icono boton-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/directores/borrar.php" class="d-inline" onsubmit="return confirm('¿Borrar director?');">
                                    <input type="hidden" name="idDirector" value="<?php echo $d['idDirector']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
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