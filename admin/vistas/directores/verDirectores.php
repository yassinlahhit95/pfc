<?php
session_start();
$titulo_pagina = "Ver Directores - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";

require_once "../../modelos/directores.php";

$listaDirectores = listarDirectores();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Directores</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/directores/agregarDirectores.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Director
        </a>
    </div>
</div>

<?php if ($error) { ?>
<div class="mensaje-error">
    <p><?php echo $error; ?></p>
</div>
<?php } ?>

<?php if ($exito) { ?>
<div class="mensaje-exito">
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

<div class="contenedor-tabla">
    <table class="tabla-datos" id="tablaDirectores">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Fecha Alta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaDirectores)) { ?>
            <tr>
                <td colspan="6" class="sin-datos">No hay directores registrados</td>
            </tr>
            <?php } else { ?>
                <?php foreach ($listaDirectores as $d) { ?>
                <tr>
                    <td><?php echo $d['idDirector']; ?></td>
                    <td><strong><?php echo $d['nombreDirector']; ?></strong></td>
                    <td><?php echo $d['emailDirector']; ?></td>
                    <td><?php echo $d['telefonoDirector'] ?? '-'; ?></td>
                    <td><?php echo $d['fechaAltaDirector']; ?></td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/directores/verDetallesDirectores.php?id=<?php echo $d['idDirector']; ?>" 
                               class="boton-icono boton-ver" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="vistas/directores/modificarDirectores.php?id=<?php echo $d['idDirector']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="../../controladores/directores/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este director?');">
                                <input type="hidden" name="idDirector" value="<?php echo $d['idDirector']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
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

<?php include '../comunes/footer.php'; ?>
