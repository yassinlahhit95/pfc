<?php
session_start();

// ValidaciÃ³n de sesiÃ³n simple
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

$titulo_pagina = "GESTIÃ“N DE MÃ“DULOS - SUPER ADMIN";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/modulos.php";

// Obtenemos la lista de todos los mÃ³dulos registrados
$listaDeModulosActuales = listarModulos();

// Captura de mensajes de Ã©xito o error
$mensajeExito = "";
if (isset($_SESSION['exito'])) {
    $mensajeExito = $_SESSION['exito'];
}

$mensajeError = "";
if (isset($_SESSION['error'])) {
    $mensajeError = $_SESSION['error'];
}

// Limpiamos los mensajes de la sesiÃ³n
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>MÃ“DULOS PROFESIONALES</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/modulos/agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO MÃ“DULO
        </a>
    </div>
</div>

<?php if (!empty($mensajeExito)) { ?>
    <div class="mensaje-exito"><?php echo $mensajeExito; ?></div>
<?php } ?>

<?php if (!empty($mensajeError)) { ?>
    <div class="mensaje-error"><?php echo $mensajeError; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaModulos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE DEL MÃ“DULO</th>
                    <th>CICLO FORMATIVO</th>
                    <th>PROFESORES ASIGNADOS</th>
                    <th>HORAS TOTALES</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeModulosActuales)) { ?>
                    <tr>
                        <td colspan="6" class="sin-datos">No hay mÃ³dulos registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeModulosActuales as $moduloIndividual) { 
                        // LÃ³gica simple para obtener los nombres de los profesores de este mÃ³dulo
                        $conexionTemporal = obtenerConexion();
                        $idModuloActual = $moduloIndividual['idModulo'];
                        
                        $sqlProfesores = "SELECT profesores.nombreProfesor 
                                          FROM profesores 
                                          JOIN profesor_modulo ON profesores.idProfesor = profesor_modulo.idProfesor 
                                          WHERE profesor_modulo.idModulo = $idModuloActual";
                                          
                        $resultadoProfesores = mysqli_query($conexionTemporal, $sqlProfesores);
                        $nombresProfesores = [];
                        
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
                                if (!empty($moduloIndividual['abreviaturaCiclo'])) {
                                    echo "<strong>[" . $moduloIndividual['abreviaturaCiclo'] . "]</strong> ";
                                }
                                echo strtoupper($moduloIndividual['nombreCiclo']); 
                            ?>
                        </td>
                        <td>
                            <?php if (empty($nombresProfesores)) { ?>
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
                                   class="btn-accion btn-editar" title="Editar mÃ³dulo">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/modulos/borrar.php" class="d-inline" onsubmit="return confirm('Â¿Eliminar este mÃ³dulo?')">
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

