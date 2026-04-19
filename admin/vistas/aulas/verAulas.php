<?php
session_start();
$titulo_pagina = "Gestión de Aulas - Super Admin";
$seccion = 'aulas';
include_once "../comunes/nav.php";

require_once "../../modelos/aulas.php";

$listaAulas = listarAulas();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_aulas'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Gestión de Aulas</h1>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><p><?php echo $exito; ?></p></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><p><?php echo $error; ?></p></div>
<?php } ?>

<!-- Formulario en una fila superior -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nueva Aula</h3>
    </div>
    <form method="POST" action="controladores/aulas/insertar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Nombre del Aula</label>
            <input type="text" name="nombreAula" value="<?php echo $datos['nombreAula'] ?? ''; ?>" placeholder="Ej: Aula 101" class="<?php echo isset($errores['nombreAula']) ? 'input-error' : ''; ?>">
            <?php if (isset($errores['nombreAula'])) { ?>
                <p class="error-campo"><?php echo $errores['nombreAula']; ?></p>
            <?php } ?>
        </div>
        <div class="mt-25">
            <button type="submit" name="guardarAula" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Aula
            </button>
        </div>
    </form>
</div>

<!-- Tabla debajo -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Aulas Registradas</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Aula</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaAulas)) { ?>
                    <tr><td colspan="3" class="sin-datos">No hay aulas registradas</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaAulas as $aula) { ?>
                    <tr>
                        <td><?php echo $aula['idAula']; ?></td>
                        <td><strong><?php echo $aula['nombreAula']; ?></strong></td>
                        <td>
                            <form action="controladores/aulas/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta aula?');">
                                <input type="hidden" name="idAula" value="<?php echo $aula['idAula']; ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="boton-icono boton-eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
