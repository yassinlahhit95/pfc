<?php
session_start();

// Validación de sesión simple
if (isset($_SESSION['idAdmin']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "GESTIÓN DE MÓDULOS - SUPER ADMIN";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";

// Obtenemos la lista de todos los módulos registrados
$listaDeModulosActuales = listarModulos();

// Captura de mensajes de éxito o error
$mensajeExito = "";
if (isset($_SESSION['exito'])) {
    $mensajeExito = $_SESSION['exito'];
}

$mensajeError = "";
if (isset($_SESSION['error'])) {
    $mensajeError = $_SESSION['error'];
}

// Limpiamos los mensajes de la sesión
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>MÓDULOS PROFESIONALES</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/modulos/agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO MÓDULO
        </a>
    </div>
</div>

<?php if ($mensajeExito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensajeExito; ?></div>
<?php } ?>

<?php if ($mensajeError != "") { ?>
    <div class="mensaje-error"><?php echo $mensajeError; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaModulos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE DEL MÓDULO</th>
                    <th>CICLO FORMATIVO</th>
                    <th>PROFESORES ASIGNADOS</th>
                    <th>HORAS TOTALES</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($listaDeModulosActuales == false || count($listaDeModulosActuales) == 0) { ?>
                    <tr>
                        <td colspan="6" class="sin-datos">No hay módulos registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeModulosActuales as $moduloIndividual) { 
                        // Lógica simple para obtener los nombres de los profesores de este módulo
                        $conexionTemporal = obtenerConexion();
                        $idModuloActual = $moduloIndividual['idModulo'];
                        
                        $sqlProfesores = "SELECT profesores.nombreProfesor 
                                          FROM profesores 
                                          JOIN profesor_modulo ON profesores.idProfesor = profesor_modulo.idProfesor 
                                          WHERE profesor_modulo.idModulo = $idModuloActual";
                                          
                        $resultadoProfesores = mysqli_query($conexionTemporal, $sqlProfesores);
                        $nombresProfesores = array();
                        
                        while($datosProfesor = mysqli_fetch_assoc($resultadoProfesores)) { 
                            $nombresProfesores[] = strtoupper($datosProfesor['nombreProfesor']); 
                        }
                        mysqli_close($conexionTemporal);
                    ?>
                    <tr>
                        <td><?php echo $moduloIndividual['idModulo']; ?></td>
                        <td><strong><?php echo strtoupper($moduloIndividual['nombreModulo']); ?></strong></td>
                        <td>
                            <?php 
                                if (isset($moduloIndividual['abreviaturaCiclo']) && $moduloIndividual['abreviaturaCiclo'] != "") {
                                    echo "<strong>[" . $moduloIndividual['abreviaturaCiclo'] . "]</strong> ";
                                }
                                echo strtoupper($moduloIndividual['nombreCiclo']); 
                            ?>
                        </td>
                        <td>
                            <?php if ($nombresProfesores == false || count($nombresProfesores) == 0) { ?>
                                <span class="texto-rojo texto-pequeno">
                                    <i class="fas fa-exclamation-triangle"></i> SIN PROFESOR
                                </span>
                            <?php } else { ?>
                                <div class="texto-pequeno">
                                    <?php echo implode(", ", $nombresProfesores); ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?php echo $moduloIndividual['horasMaximas']; ?> H</td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/modulos/asignarProfesorModulo.php?idModulo=<?php echo $moduloIndividual['idModulo']; ?>" 
                                   class="btn-accion btn-ver" title="Asignar o cambiar profesor">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </a>
                                <a href="/pfc/vistas/admin/modulos/modificarModulos.php?idModulo=<?php echo $moduloIndividual['idModulo']; ?>" 
                                   class="btn-accion btn-editar" title="Editar módulo">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/modulos/borrar.php" class="d-inline" onsubmit="return confirm('¿Eliminar este módulo?')">
                                    <input type="hidden" name="idModulo" value="<?php echo $moduloIndividual['idModulo']; ?>">
                                    <button type="submit" class="btn-accion btn-eliminar" title="Borrar">
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

