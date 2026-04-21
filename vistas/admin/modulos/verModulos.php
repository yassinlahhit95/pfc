<?php
session_start();
$titulo_pagina = "Gestión de Módulos - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
$listaModulos = listarModulos();

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
        <h1>Módulos Profesionales</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/modulos/agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Nuevo Módulo
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
                    <th>Nombre del Módulo</th>
                    <th>Ciclo Formativo</th>
                    <th>Horas Totales</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaModulos)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay módulos registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaModulos as $modulo) { ?>
                    <tr>
                        <td><?php echo $modulo['idModulo']; ?></td>
                        <td><strong><?php echo $modulo['nombreModulo']; ?></strong></td>
                        <td><?php 
                            if ($modulo['nombreCiclo']) {
                                echo $modulo['nombreCiclo'];
                            } else {
                                echo '<span class="texto-atenuado">Sin asignar</span>';
                            }
                        ?></td>
                        <td><?php echo $modulo['horasMaximas']; ?> h</td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/modulos/modificarModulos.php?idModulo=<?php echo $modulo['idModulo']; ?>" 
                                   class="boton-icono boton-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/modulos/borrar.php" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este módulo?');">
                                    <input type="hidden" name="idModulo" value="<?php echo $modulo['idModulo']; ?>">
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
</div>

<?php include '../comunes/footer.php'; ?>