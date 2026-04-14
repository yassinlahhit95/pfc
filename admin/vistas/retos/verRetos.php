<?php
session_start();
$titulo_pagina = "Ver Retos - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/retos.php";

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();

$retoObj = new reto($conexion);
$listaRetos = $retoObj->listarRetosModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Retos</h1>
        <p class="subtitulo-encabezado">Gestión de retos y desafíos</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/agregarRetos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Reto
        </a>
    </div>
</div>

<?php if ($exito): ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo htmlspecialchars($exito); ?></p>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo htmlspecialchars($error); ?></p>
</div>
<?php endif; ?>

<div class="contenedor-tabla">
    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Reto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Horas</th>
                <th>Módulos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaRetos)): ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay retos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaRetos as $r) { 
                    $modulosDeReto = $retoObj->obtenerModulosDeReto($r['idReto']);
                    $nombresModulos = array_column($modulosDeReto, 'nombreModulo');
                ?>
                <tr>
                    <td><?php echo $r['idReto']; ?></td>
                    <td><?php echo htmlspecialchars($r['nombreReto']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($r['fechaInicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($r['fechaFin'])); ?></td>
                    <td><?php echo $r['horasReto']; ?> h</td>
                    <td><?php echo htmlspecialchars(implode(', ', $nombresModulos)); ?></td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/retos/calificarReto.php?id=<?php echo $r['idReto']; ?>" 
                               class="boton-icono boton-ver" title="Calificar Estudiantes">
                                <i class="fas fa-graduation-cap"></i>
                            </a>
                            <a href="vistas/retos/modificarRetos.php?id=<?php echo $r['idReto']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controlador/retosControlador.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este reto?');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idReto" value="<?php echo $r['idReto']; ?>">
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
