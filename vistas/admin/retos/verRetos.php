<?php
session_start();
$titulo_pagina = "Gestión de Retos - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/retos.php";
require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/modulos.php";

$listaRetos = listarRetos();

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
        <h1>Gestión de Retos</h1>
        <p class="subtitulo-encabezado">Listado y seguimiento de proyectos colaborativos</p>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/retos/agregarRetos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Crear Nuevo Reto
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
                    <th>Nombre Reto</th>
                    <th>Módulos Asociados</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Final</th>
                    <th>Horas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaRetos)) { ?>
                    <tr><td colspan="7" class="sin-datos">No hay retos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaRetos as $reto) { 
                        $modulosReto = obtenerModulosDeReto($reto['idReto']);
                    ?>
                    <tr>
                        <td><?php echo $reto['idReto']; ?></td>
                        <td><strong><?php echo $reto['nombreReto']; ?></strong></td>
                        <td>
                            <?php if (empty($modulosReto)) { echo '-'; } ?>
                            <?php foreach ($modulosReto as $m) { ?>
                                <span class="etiqueta-estado azul" style="display: inline-block; margin-bottom: 2px;">
                                    <?php echo $m['nombreModulo']; ?>
                                </span>
                            <?php } ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($reto['fechaInicio'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reto['fechaFin'])); ?></td>
                        <td><strong><?php echo $reto['horasReto']; ?>h</strong></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/retos/modificarRetos.php?idReto=<?php echo $reto['idReto']; ?>" 
                                   class="boton-icono boton-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/retos/borrar.php" class="d-inline" onsubmit="return confirm('¿Eliminar reto?');">
                                    <input type="hidden" name="idReto" value="<?php echo $reto['idReto']; ?>">
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