<?php
session_start();
$titulo_pagina = "Gestión de Anuncios";
$seccion = 'anuncios';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/anuncios.php";

$con = new Conexion();
$conexionBD = $con->conectar();
$modeloAnuncios = new anuncio($conexionBD);

$listaAnuncios = $modeloAnuncios->listarAnunciosModelo();

$exito = $_SESSION['exito'] ?? '';
$error_titulo = $_SESSION['error_nombre'] ?? '';
unset($_SESSION['exito'], $_SESSION['error_nombre']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Tablón de Anuncios</h1>
        <p class="texto-atenuado">Comunicados del centro educativo</p>
    </div>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $exito; ?></div>
<?php } ?>

<div class="disposicion-flexible separacion-grande">
    <!-- Formulario -->
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta"><h3>Nuevo Anuncio</h3></div>
        <form method="POST" action="controlador/anunciosControlador.php">
            <input type="hidden" name="accion" value="insertar">
            
            <div class="campo-formulario margen-abajo">
                <label>Título</label>
                <input type="text" name="titulo" placeholder="Ej: Próximos Exámenes">
                <?php if ($error_titulo != "") { echo "<p class='error-campo'>$error_titulo</p>"; } ?>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Mensaje</label>
                <textarea name="mensaje" rows="4"></textarea>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Fecha Expiración</label>
                <input type="date" name="fecha_expiracion" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <button type="submit" class="boton-azul ancho-total">Publicar</button>
        </form>
    </div>

    <!-- Lista -->
    <div class="tarjeta-blanca flexible-rellenar">
        <div class="titulo-tarjeta"><h3>Historial</h3></div>
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaAnuncios)) { ?>
                        <tr><td colspan="3" class="sin-datos">No hay anuncios</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaAnuncios as $a) { 
                            $hoy = date('Y-m-d');
                            $esActivo = ($a['fechaExpiracion'] >= $hoy);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($a['titulo']); ?></strong><br>
                                <small class="texto-atenuado">Expira: <?php echo $a['fechaExpiracion']; ?></small>
                            </td>
                            <td>
                                <span class="estado-bolita <?php echo $esActivo ? 'activo-verde' : 'inactivo-rojo'; ?>">
                                    <?php echo $esActivo ? 'Activo' : 'Expirado'; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="controlador/anunciosControlador.php" class="d-inline">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?php echo $a['idAnuncio']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar" onclick="return confirm('¿Borrar?');">
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
