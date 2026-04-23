<?php
session_start();
$titulo_pagina = "Gestión de Pagos - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../../modelos/pagos.php";
require_once "../../../modelos/estudiantes.php";

$todos_los_pagos = listarPagos();
$todos_los_estudiantes = listarEstudiantes();

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$mensaje_exito = "";
if (isset($_SESSION['exito'])) { $mensaje_exito = $_SESSION['exito']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_pago'])) { $datos = $_SESSION['datos_pago']; }

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_pago']);
?>

<div class="encabezado-pagina">
    <h1>Gestión de Pagos</h1>
</div>

<?php if ($mensaje_exito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensaje_exito; ?></div>
<?php } ?>
<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Registrar Nuevo Pago</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/pagos/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccionar Estudiante --</option>
                    <?php foreach ($todos_los_estudiantes as $estudiante) { ?>
                        <option value="<?php echo $estudiante['idEstudiante']; ?>" <?php if(isset($datos['idEstudiante']) && $datos['idEstudiante'] == $estudiante['idEstudiante']) echo "selected"; ?>>
                            <?php echo $estudiante['nombreEstudiante']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['idEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Concepto *</label>
                <input type="text" name="conceptoPago" value="<?php if(isset($datos['conceptoPago'])) echo $datos['conceptoPago']; ?>" placeholder="Ej: Matrícula Segundo Semestre">
                <?php if (isset($lista_de_errores['conceptoPago'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['conceptoPago']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Cantidad *</label>
                <input type="text" name="cantidadPago" value="<?php if(isset($datos['cantidadPago'])) echo $datos['cantidadPago']; ?>" placeholder="0.00">
                <?php if (isset($lista_de_errores['cantidadPago'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['cantidadPago']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Pago *</label>
                <input type="date" name="fechaPago" value="<?php if(isset($datos['fechaPago'])) echo $datos['fechaPago']; ?>">
                <?php if (isset($lista_de_errores['fechaPago'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaPago']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarPago" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Pago
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_pagos)) { ?>
                    <tr><td colspan="5" class="sin-datos">No se han registrado pagos</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_pagos as $pago) { ?>
                    <tr>
                        <td><strong><?php echo $pago['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $pago['conceptoPago']; ?></td>
                        <td><?php echo number_format($pago['cantidadPago'], 2); ?> €</td>
                        <td><?php echo date('d/m/Y', strtotime($pago['fechaPago'])); ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/pagos/modificarPagos.php?idPago=<?php echo $pago['idPago']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/pagos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro de pago?')">
                                    <input type="hidden" name="idPago" value="<?php echo $pago['idPago']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
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
