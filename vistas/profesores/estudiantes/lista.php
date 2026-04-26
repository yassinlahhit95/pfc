<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$estudiantes = listarEstudiantes();

$tituloDelPagina = "Lista de Estudiantes - Portal Profesores";
$seccionActual = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Gestión de Estudiantes</h1>
</div>

<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label><i class="fas fa-search"></i> BUSCAR ALUMNO:</label>
        <input type="text" id="inputBuscarEst" placeholder="Busque por nombre, email, DNI o ciclo..." onkeyup="filtrarTabla('inputBuscarEst', 'tablaEstudiantesProf')">
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

