<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesor = $_SESSION['idProfesor'];
$estudiantes = listarEstudiantesPorProfesor($idProfesor);
$listaDeCiclosParaFiltro = obtenerCiclosDeProfesor($idProfesor);

$tituloDelPagina = "Lista de Estudiantes - Portal Profesores";
$seccionActual = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Gestión de Estudiantes</h1>
</div>

<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label><i class="fas fa-filter"></i> FILTRAR POR CICLO:</label>
        <select id="selectFiltroCicloProf" onchange="filtrarTabla('selectFiltroCicloProf', 'tablaEstudiantesProf')">
            <option value="">-- Todos los Ciclos --</option>
            <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                <option value="<?php echo strtoupper($cicloFiltro['nombreCiclo']); ?>">
                    <?php echo strtoupper($cicloFiltro['nombreCiclo']); ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Estudiantes Registrados</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantesProf">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>DNI</th>
                    <th>Ciclo</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($estudiantes) { ?>
                    <?php foreach ($estudiantes as $est) { ?>
                        <tr>
                            <td class="texto-negrita"><?php echo $est['nombreEstudiante']; ?></td>
                            <td><?php echo $est['emailEstudiante']; ?></td>
                            <td><?php echo $est['dniEstudiante']; ?></td>
                            <td><?php echo $est['nombreCiclo']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay estudiantes registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

