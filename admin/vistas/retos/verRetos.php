<?php
session_start();
$titulo_pagina = "Ver Retos - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/retos.php";
require_once "../../modelos/ciclos.php";
require_once "../../modelos/modulos.php";

$retoObj = new reto();
$listaRetos = $retoObj->listarRetosModelo();

$cicloObj = new ciclo();
$listaCiclos = $cicloObj->listarCiclosModelo();

$moduloObj = new modulo();
$listaModulos = $moduloObj->listarModulosModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Retos</h1>
        <p class="subtitulo-encabezado">Gestión de retos y desafíos educativos</p>
    </div>
    <div class="acciones-pagina">
        <div class="disposicion-flexible separacion-pequena">
            <select id="filtroCiclo" class="selector-filtro">
                <option value="">Filtrar por Ciclo</option>
                <?php foreach ($listaCiclos as $c) { ?>
                    <option value="<?php echo $c['idCiclo']; ?>">
                        <?php echo htmlspecialchars($c['nombreCiclo']); ?>
                    </option>
                <?php } ?>
            </select>

            <select id="filtroModulo" class="selector-filtro">
                <option value="">Filtrar por Módulo</option>
                <?php foreach ($listaModulos as $m) { ?>
                    <option value="<?php echo $m['idModulo']; ?>" data-ciclo="<?php echo $m['idCiclo']; ?>">
                        <?php echo htmlspecialchars($m['nombreModulo']); ?>
                    </option>
                <?php } ?>
            </select>

            <a href="vistas/retos/agregarRetos.php" class="boton-primario">
                <i class="fas fa-plus"></i> Agregar Reto
            </a>
        </div>
    </div>
</div>

<?php if ($exito): ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo htmlspecialchars($exito); ?></p>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo htmlspecialchars($error); ?></p>
</div>
<?php endif; ?>

<div class="contenedor-tabla">
    <table class="tabla-datos" id="tablaRetos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Reto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Horas</th>
                <th>Módulos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaRetos)): ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay retos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaRetos as $r) { 
                    $modulosDeReto = $retoObj->obtenerModulosDeReto($r['idReto']);
                    $nombresModulos = array_column($modulosDeReto, 'nombreModulo');
                    $idsModulos = array_column($modulosDeReto, 'idModulo');
                    $idsCiclos = array_unique(array_column($modulosDeReto, 'idCiclo'));
                ?>
                <tr data-modulos="<?php echo implode(',', $idsModulos); ?>" 
                    data-ciclos="<?php echo implode(',', $idsCiclos); ?>">
                    <td><?php echo $r['idReto']; ?></td>
                    <td><strong><?php echo htmlspecialchars($r['nombreReto']); ?></strong></td>
                    <td><?php echo date('d/m/Y', strtotime($r['fechaInicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($r['fechaFin'])); ?></td>
                    <td><span class="etiqueta-contador" style="background: #edf2f7; color: #2d3748;"><?php echo $r['horasReto']; ?> h</span></td>
                    <td>
                        <div style="max-width: 250px; font-size: 12px; color: #718096;">
                            <?php echo htmlspecialchars(implode(', ', $nombresModulos)); ?>
                        </div>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/retos/calificarReto.php?id=<?php echo $r['idReto']; ?>" 
                               class="boton-icono boton-ver" title="Calificar Estudiantes">
                                <i class="fas fa-graduation-cap"></i>
                            </a>
                            <a href="vistas/retos/modificarRetos.php?id=<?php echo $r['idReto']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/retos/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este reto?');">
                                <input type="hidden" name="idReto" value="<?php echo $r['idReto']; ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroCiclo = document.getElementById('filtroCiclo');
    const filtroModulo = document.getElementById('filtroModulo');
    const tablaFilas = document.querySelectorAll('#tablaRetos tbody tr');
    const modulosOptions = Array.from(filtroModulo.options);

    function filtrar() {
        const cicloId = filtroCiclo.value;
        const moduloId = filtroModulo.value;

        // Actualizar opciones de módulos según ciclo
        modulosOptions.forEach(opt => {
            if (opt.value === "") return;
            const optCiclo = opt.getAttribute('data-ciclo');
            if (cicloId === "" || optCiclo === cicloId) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });

        // Filtrar filas de la tabla
        tablaFilas.forEach(fila => {
            if (fila.classList.contains('sin-datos')) return;
            
            const ciclosFila = fila.getAttribute('data-ciclos').split(',');
            const modulosFila = fila.getAttribute('data-modulos').split(',');
            
            const coincideCiclo = cicloId === "" || ciclosFila.includes(cicloId);
            const coincideModulo = moduloId === "" || modulosFila.includes(moduloId);

            if (coincideCiclo && coincideModulo) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    }

    filtroCiclo.addEventListener('change', () => {
        filtroModulo.value = ""; // Reset modulo filter when cycle changes
        filtrar();
    });
    filtroModulo.addEventListener('change', filtrar);
});
</script>

<?php include '../comunes/footer.php'; ?>
