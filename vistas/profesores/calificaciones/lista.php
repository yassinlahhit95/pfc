<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = $_SESSION['idProfesor'];

$idCiclo = isset($_GET['idCiclo']) ? intval($_GET['idCiclo']) : 0;
$idModulo = isset($_GET['idModulo']) ? intval($_GET['idModulo']) : 0;

// Filtros disponibles para el profesor
$mis_ciclos = obtenerCiclosDeProfesor($idProfesor);
$mis_modulos = [];
if ($idCiclo > 0) {
    $mis_modulos = obtenerModulosPorCiclo($idCiclo);
} else {
    $mis_modulos = obtenerModulosDeProfesor($idProfesor);
}

$calificaciones = listarCalificacionesPorProfesorFiltrado($idProfesor, $idCiclo, $idModulo);

$tituloDelPagina = "Calificaciones - Portal Profesores";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Notas de Alumnos</h1>
    <a href="/pfc/vistas/profesores/calificaciones/agregar.php" class="boton-primario">Asignar Nota</a>
</div>

<div class="tarjeta-blanca margen-abajo">
    <form method="GET" action="" class="disposicion-flexible alinear-fin separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Filtrar por Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="0">-- Todos mis Ciclos --</option>
                <?php foreach ($mis_ciclos as $c) { ?>
                    <option value="<?php echo $c['idCiclo']; ?>" <?php if($idCiclo == $c['idCiclo']) echo "selected"; ?>>
                        <?php echo $c['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo-formulario flexible-rellenar">
            <label>Filtrar por MÃ³dulo:</label>
            <select name="idModulo" onchange="this.form.submit()">
                <option value="0">-- Todos mis MÃ³dulos --</option>
                <?php foreach ($mis_modulos as $m) { ?>
                    <option value="<?php echo $m['idModulo']; ?>" <?php if($idModulo == $m['idModulo']) echo "selected"; ?>>
                        <?php echo $m['nombreModulo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-15">
            <a href="lista.php" class="boton-secundario">Limpiar</a>
        </div>
    </form>
</div>

<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label><i class="fas fa-search"></i> BUSCAR EN RESULTADOS:</label>
        <input type="text" id="inputBuscarNota" placeholder="Escriba nombre del alumno o mÃ³dulo..." onkeyup="filtrarTabla('inputBuscarNota', 'tablaNotasProf')">
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaNotasProf">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>MÃ³dulo</th>
                    <th>1Âª Ev</th>
                    <th>1Âª Final</th>
                    <th>2Âª Ev</th>
                    <th>2Âª Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($calificaciones) { ?>
                    <?php foreach ($calificaciones as $nota) { ?>
                        <tr>
                            <td><?php echo $nota['nombreEstudiante']; ?></td>
                            <td class="texto-negrita"><?php echo $nota['nombreModulo']; ?></td>
                            <td><?php echo $nota['nota_1ev']; ?></td>
                            <td class="texto-negrita"><?php echo $nota['nota_1final']; ?></td>
                            <td><?php echo $nota['nota_2ev']; ?></td>
                            <td class="texto-negrita"><?php echo $nota['nota_2final']; ?></td>
                            <td>
                                <div class="botones-accion">
                                    <a href="/pfc/vistas/profesores/calificaciones/editar.php?id=<?php echo $nota['idCalificacion']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="sin-datos">No hay calificaciones que coincidan con los filtros.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>


