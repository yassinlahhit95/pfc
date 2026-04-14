<?php
session_start();
$titulo_pagina = "Ver Ciclos - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../modelos/ciclos.php";

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();

$cicloObj = new ciclo($conexion);
$listaCiclos = $cicloObj->listarCiclosModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Ciclos</h1>
        <p class="subtitulo-encabezado">Gestión de ciclos formativos</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/ciclos/agregarCiclos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Ciclo
        </a>
    </div>
</div>

<?php if ($exito): ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo htmlspecialchars($exito); ?></p>
</div>
<?php endif; ?>

<div class="contenedor-tabla">
    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Ciclo</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaCiclos)): ?>
            <tr>
                <td colspan="4" class="sin-datos">No hay ciclos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaCiclos as $c) { ?>
                <tr>
                    <td><?php echo $c['idCiclo']; ?></td>
                    <td><?php echo htmlspecialchars($c['nombreCiclo']); ?></td>
                    <td><?php echo htmlspecialchars($c['descripcionCiclo']); ?></td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/ciclos/modificarCiclos.php?id=<?php echo $c['idCiclo']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controlador/ciclosControlador.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este ciclo? Se borrarán sus módulos y retos asociados.');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idCiclo" value="<?php echo $c['idCiclo']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../comunes/footer.php'; ?>
