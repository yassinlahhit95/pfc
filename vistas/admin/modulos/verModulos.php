<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "GESTIÓN DE MÓDULOS - ADMIN";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

$listaDeModulosActuales = listarModulos();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>MÓDULOS PROFESIONALES</h1>
    </div>
    <div class="acciones-pagina">
        <a href="agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO MÓDULO
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
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
                <?php if (empty($listaDeModulosActuales)) { ?>
                    <tr>
                        <td colspan="6" class="sin-datos">No hay módulos registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeModulosActuales as $moduloIndividual) { ?>
                    <?php
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
                        <td><?= $moduloIndividual['idModulo'] ?></td>
                        <td><strong><?= strtoupper($moduloIndividual['nombreModulo']) ?></strong></td>
                        <td>
                            <?php if (!empty($moduloIndividual['abreviaturaCiclo'])) { ?>
                                <strong>[<?= $moduloIndividual['abreviaturaCiclo'] ?>]</strong> 
                            <?php } ?>
                            <?= strtoupper($moduloIndividual['nombreCiclo']) ?>
                        </td>
                        <td>
                            <?php if (empty($nombresProfesores)) { ?>
                                <span class="texto-rojo texto-pequeno">
                                    <i class="fas fa-exclamation-triangle"></i> SIN PROFESOR
                                </span>
                            <?php } else { ?>
                                <div class="texto-pequeno">
                                    <?= implode(", ", $nombresProfesores) ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?= $moduloIndividual['horasMaximas'] ?> H</td>
                        <td>
                            <div class="botones-accion">
                                <a href="asignarProfesorModulo.php?idModulo=<?= $moduloIndividual['idModulo'] ?>"
                                   class="btn-accion btn-ver" title="Asignar o cambiar profesor">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </a>
                                <a href="modificarModulos.php?idModulo=<?= $moduloIndividual['idModulo'] ?>" 
                                   class="btn-accion btn-editar" title="Editar módulo">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="../../../controladores/admin/modulos/borrar.php" class="d-inline" onsubmit="return confirm('¿Eliminar este módulo?')">
                                    <input type="hidden" name="idModulo" value="<?= $moduloIndividual['idModulo'] ?>">
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



