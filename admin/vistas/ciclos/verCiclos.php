<?php
session_start();
$titulo_pagina = "Ver Ciclos - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../modelos/ciclos.php";

$cicloObj = new ciclo();
$listaCiclos = $cicloObj->listarCiclosModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Ciclos</h1>
        <p class="subtitulo-encabezado">Gestión de ciclos formativos</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/ciclos/agregarCiclos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Ciclo
        </a>
    </div>
</div>

<?php if ($exito): ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo htmlspecialchars($exito); ?></p>
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
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaCiclos)): ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay ciclos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaCiclos as $c) { 
                    $estado = $c['idEstado'] == 1 ? 'Activo' : 'Inactivo';
                    $claseEstado = $c['idEstado'] == 1 ? 'estado-activo' : 'estado-inactivo';
                    
                    // Procesar nombres de profesores
                    $nombresProfesores = array_column($c['profesores'] ?? [], 'nombreProfesor');
                    $textoProfesores = !empty($nombresProfesores) ? implode(', ', $nombresProfesores) : 'Sin asignar';
                    
                    // Procesar nombres de aulas
                    $nombresAulas = array_column($c['aulas'] ?? [], 'nombreAula');
                    $textoAulas = !empty($nombresAulas) ? implode(', ', $nombresAulas) : 'Sin asignar';
                ?>
                <tr>
                    <td><?php echo $c['idCiclo']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['nombreCiclo']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['nombreNivel'] ?? 'N/A'); ?></td>
                    <td class="texto-pequeno"><?php echo htmlspecialchars($textoProfesores); ?></td>
                    <td class="texto-pequeno"><?php echo htmlspecialchars($textoAulas); ?></td>
                    <td>
                        <span class="insignia-estado <?php echo $claseEstado; ?>">
                            <?php echo $estado; ?>
                        </span>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/ciclos/modificarCiclos.php?id=<?php echo $c['idCiclo']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/ciclos/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este ciclo? Se borrarán sus módulos y retos asociados.');">
                                <input type="hidden" name="idCiclo" value="<?php echo $c['idCiclo']; ?>">
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
