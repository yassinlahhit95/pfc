<?php
session_start();
$titulo_pagina = "Ver Módulos - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../modelos/modulos.php";
$listaModulos = listarModulos();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Módulos</h1>
        <p class="subtitulo-encabezado">Gestión de módulos educativos</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/modulos/agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Módulo
        </a>
    </div>
</div>

<?php if ($exito) { ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

<div class="contenedor-tabla">
    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Módulo</th>
                <th>Ciclo</th>
                <th>Horas Máximas</th>
                <th>Horas Usadas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaModulos)) { ?>
            <tr>
                <td colspan="6" class="sin-datos">No hay módulos registrados</td>
            </tr>
            <?php } else { ?>
                <?php foreach ($listaModulos as $modulo) { 
                    $horasUsadas = obtenerHorasTotalesRetosModulo($modulo['idModulo']);
                ?>
                <tr>
                    <td><?php echo $modulo['idModulo']; ?></td>
                    <td><strong><?php echo $modulo['nombreModulo']; ?></strong></td>
                    <td><?php echo $modulo['nombreCiclo']; ?></td>
                    <td><?php echo $modulo['horasMaximas']; ?> h</td>
                    <td>
                        <?php echo $horasUsadas; ?> h
                        <?php 
                            $porcentaje = ($modulo['horasMaximas'] > 0) ? ($horasUsadas / $modulo['horasMaximas']) * 100 : 0;
                            $colorClase = $porcentaje > 100 ? 'inactivo-rojo' : 'activo-verde';
                        ?>
                        <div class="barra-progreso-contenedor">
                            <div class="barra-progreso <?php echo $colorClase; ?>" style="width: <?php echo min($porcentaje, 100); ?>%;"></div>
                        </div>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/modulos/modificarModulos.php?idModulo=<?php echo $modulo['idModulo']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/modulos/borrar.php" 
                                  class="form-eliminar d-inline"
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

<?php include '../comunes/footer.php'; ?>
