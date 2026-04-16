<?php
session_start();
$titulo_pagina = "Ver Módulos - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../modelos/modulos.php";

$moduloObj = new modulo();
$listaModulos = $moduloObj->listarModulosModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Módulos</h1>
        <p class="subtitulo-encabezado">Gestión de módulos profesionales</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/modulos/agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Módulo
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
                <th>Nombre Módulo</th>
                <th>Ciclo</th>
                <th>Horas Máximas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaModulos)): ?>
            <tr>
                <td colspan="5" class="sin-datos">No hay módulos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaModulos as $m) { ?>
                <tr>
                    <td><?php echo $m['idModulo']; ?></td>
                    <td><?php echo htmlspecialchars($m['nombreModulo']); ?></td>
                    <td><?php echo htmlspecialchars($m['nombreCiclo']); ?></td>
                    <td><?php echo $m['horasMaximas']; ?> h</td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/modulos/modificarModulos.php?id=<?php echo $m['idModulo']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/modulos/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este módulo?');">
                                <input type="hidden" name="idModulo" value="<?php echo $m['idModulo']; ?>">
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
