<?php
session_start();
$titulo_pagina = "Gestión de Reclamaciones - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../../modelos/reclamaciones.php";
require_once "../../../modelos/estudiantes.php";

$todas_las_reclamaciones = listarReclamaciones();
$todos_los_estudiantes = listarEstudiantes();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reclamacion'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_reclamacion']);
?>

<div class="encabezado-pagina">
    <h1>Reclamaciones</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nueva Reclamación Administrativa</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/reclamaciones/insertar.php">
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
                <label>Asunto *</label>
                <input type="text" name="asuntoReclamacion" value="<?php if(isset($datos['asuntoReclamacion'])) echo $datos['asuntoReclamacion']; ?>" placeholder="Ej: Error en cuota mensual">
                <?php if (isset($lista_de_errores['asuntoReclamacion'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['asuntoReclamacion']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción *</label>
                <textarea name="descripcionReclamacion" rows="4"><?php if(isset($datos['descripcionReclamacion'])) echo $datos['descripcionReclamacion']; ?></textarea>
                <?php if (isset($lista_de_errores['descripcionReclamacion'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['descripcionReclamacion']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarReclamacion" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Reclamación
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
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todas_las_reclamaciones)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay reclamaciones registradas</td></tr>
                <?php } else { ?>
                    <?php foreach ($todas_las_reclamaciones as $rec) { ?>
                    <tr>
                        <td><strong><?php echo $rec['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $rec['asunto']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($rec['fecha'])); ?></td>
                        <td>
                            <?php 
                            $clase_estado = "contador-neutral";
                            if ($rec['estadoReclamacion'] == 'atendido') { $clase_estado = "activo-verde"; }
                            if ($rec['estadoReclamacion'] == 'pendiente') { $clase_estado = "inactivo-rojo"; }
                            ?>
                            <span class="estado-bolita <?php echo $clase_estado; ?>">
                                <?php echo $rec['estadoReclamacion']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/reclamaciones/modificarReclamacion.php?idReclamacion=<?php echo $rec['idReclamacion']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/reclamaciones/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta reclamación?')">
                                    <input type="hidden" name="idReclamacion" value="<?php echo $rec['idReclamacion']; ?>">
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
