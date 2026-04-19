<?php
session_start();
$titulo_pagina = "Ver Ciclos - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../modelos/ciclos.php";
require_once "../../modelos/profesores.php";
require_once "../../modelos/aulas.php";

$listaCiclos = listarTodosLosCiclos();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Ciclos</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/ciclos/agregarCiclos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Ciclo
        </a>
    </div>
</div>

<?php if ($exito): ?>
<div class="mensaje-exito">
    <p><?php echo $exito; ?></p>
</div>
<?php endif; ?>

<div class="contenedor-tabla">
    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Ciclo</th>
                <th>Nivel</th>
                <th>Tutor(es)</th>
                <th>Aula(s)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaCiclos)): ?>
            <tr>
                <td colspan="6" class="sin-datos">No hay ciclos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaCiclos as $ciclo) { 
                    // Recuperar nombres de profesores
                    $idsProfes = obtenerProfesoresDeUnCiclo($ciclo['idCiclo']);
                    $nombresProfes = [];
                    foreach ($idsProfes as $idP) {
                        $p = obtenerProfesorPorId($idP['idProfesor']);
                        if ($p) $nombresProfes[] = $p['nombreProfesor'];
                    }
                    $textoProfesores = !empty($nombresProfes) ? implode(', ', $nombresProfes) : 'Sin asignar';
                    
                    // Recuperar aulas
                    $idsAulas = obtenerAulasDeUnCiclo($ciclo['idCiclo']);
                    $nombresAulas = [];
                    foreach ($idsAulas as $idA) {
                        $listaA = listarAulas();
                        foreach ($listaA as $aula) {
                            if ($aula['idAula'] == $idA['idAula']) $nombresAulas[] = $aula['nombreAula'];
                        }
                    }
                    $textoAulas = !empty($nombresAulas) ? implode(', ', $nombresAulas) : 'Sin asignar';
                ?>
                <tr>
                    <td><?php echo $ciclo['idCiclo']; ?></td>
                    <td><strong><?php echo $ciclo['nombreCiclo']; ?></strong></td>
                    <td><?php echo $ciclo['nombreNivel'] ?? 'N/A'; ?></td>
                    <td><div class="texto-pequeno texto-atenuado lh-1-4"><?php echo $textoProfesores; ?></div></td>
                    <td><div class="texto-pequeno texto-atenuado lh-1-4"><?php echo $textoAulas; ?></div></td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/ciclos/modificarCiclos.php?idCiclo=<?php echo $ciclo['idCiclo']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/ciclos/borrar.php" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este ciclo?');">
                                <input type="hidden" name="idCiclo" value="<?php echo $ciclo['idCiclo']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../comunes/footer.php'; ?>
