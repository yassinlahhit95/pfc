<?php
session_start();
$titulo_pagina = "Gestión de Aulas - Super Admin";
$seccion = 'aulas';
include_once "../comunes/nav.php";

require_once "../../../modelos/aulas.php";

$todas_las_aulas = listarAulas();

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

$lista_de_errores = array();
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = array();
if (isset($_SESSION['datos_aulas'])) { $datos = $_SESSION['datos_aulas']; }

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="encabezado-pagina">
    <h1>Aulas del Centro</h1>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>


<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nueva Aula</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/aulas/insertar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Nombre del Aula</label>
            <input type="text" name="nombreAula" value="<?php if(isset($datos['nombreAula'])) { echo $datos['nombreAula']; } ?>" placeholder="Aula 101">
            <?php if (isset($lista_de_errores['nombreAula'])) { ?>
                <p class="error-campo"><?php echo $lista_de_errores['nombreAula']; ?></p>
            <?php } ?>
        </div>
        <div class="mt-25">
            <button type="submit" name="guardarAula" class="boton-primario">
                <i class="fas fa-save"></i> Registrar
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="campo-formulario mb-20">
        <label><i class="fas fa-search"></i> BUSCAR AULA:</label>
        <input type="text" id="inputBuscarAula" placeholder="Escriba nombre del aula..." onkeyup="filtrarTabla('inputBuscarAula', 'tablaAulas')">
    </div>

    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAulas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todas_las_aulas)) { ?>
                    <tr><td colspan="3" class="sin-datos">No hay aulas configuradas</td></tr>
                <?php } else { ?>
                    <?php foreach ($todas_las_aulas as $aula) { ?>
                    <tr>
                        <td><?php echo $aula['idAula']; ?></td>
                        <td><strong><?php echo $aula['nombreAula']; ?></strong></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/aulas/modificarAulas.php?idAula=<?php echo $aula['idAula']; ?>" 
                                                                   class="btn-accion btn-editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="/pfc/controladores/admin/aulas/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta aula?')">
                                                                    <input type="hidden" name="idAula" value="<?php echo $aula['idAula']; ?>">
                                                                    <button type="submit" class="btn-accion btn-eliminar">
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

