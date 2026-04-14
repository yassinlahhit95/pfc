<?php
session_start();
$titulo_pagina = "Gestión de Aulas - Super Admin";
$seccion = 'aulas';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/aulas.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();

$modeloAula = new aula($conexionBD);
$listaAulas = $modeloAula->listarAulasModelo();

// Captura de errores y persistencia
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_aulas'] ?? [];
$mensajeExito = $_SESSION['exito'] ?? '';
unset($_SESSION['errores'], $_SESSION['datos_aulas'], $_SESSION['exito']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Gestión de Aulas</h1>
        <p class="texto-atenuado">Administración de espacios físicos</p>
    </div>
</div>

<?php if ($mensajeExito) { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $mensajeExito; ?></div>
<?php } ?>

<div class="disposicion-flexible separacion-grande">
    <!-- Formulario para agregar aula -->
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta">
            <h3><i class="fas fa-plus"></i> Nueva Aula</h3>
        </div>
        <form method="POST" action="controlador/aulasControlador.php">
            <input type="hidden" name="accion" value="insertar">
            
            <div class="campo-formulario margen-abajo">
                <label>Nombre del Aula</label>
                <input type="text" name="nombreAula" 
                       value="<?php echo htmlspecialchars($datos['nombreAula'] ?? ''); ?>" 
                       placeholder="Ej: Aula 101">
                <?php if (isset($errores['nombreAula'])) { ?>
                    <p style="color: red;"><?php echo $errores['nombreAula']; ?></p>
                <?php } ?>
            </div>

            <button type="submit" name="guardarAula" class="boton-azul ancho-total">Guardar Aula</button>
        </form>
    </div>

    <!-- Lista de aulas -->
    <div class="tarjeta-blanca flexible-rellenar">
        <div class="titulo-tarjeta">
            <h3>Aulas Registradas</h3>
        </div>
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Aula</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaAulas)) { ?>
                        <tr><td colspan="3" class="sin-datos">No hay aulas registradas</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaAulas as $aula) { ?>
                        <tr>
                            <td><?php echo $aula['idAula']; ?></td>
                            <td><strong><?php echo htmlspecialchars($aula['nombreAula']); ?></strong></td>
                            <td>
                                <form method="POST" action="controlador/aulasControlador.php" class="d-inline" onsubmit="return confirm('¿Eliminar aula?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="idAula" value="<?php echo $aula['idAula']; ?>">
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
</div>

<?php include '../comunes/footer.php'; ?>
