<?php
session_start();
$titulo_pagina = "Ver Retos - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/retos.php";
require_once "../../modelos/ciclos.php";
require_once "../../modelos/modulos.php";

$listaRetos = listarRetos();
$listaCiclos = listarTodosLosCiclos();
$listaModulos = listarModulos();

$exito = $_SESSION['exito'] ?? "";
$error = $_SESSION['error'] ?? "";
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
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?php echo $ciclo['idCiclo']; ?>">
                        <?php echo $ciclo['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>

            <select id="filtroModulo" class="selector-filtro">
                <option value="">Filtrar por Módulo</option>
                <?php foreach ($listaModulos as $modulo) { ?>
                    <option value="<?php echo $modulo['idModulo']; ?>" data-ciclo="<?php echo $modulo['idCiclo']; ?>">
                        <?php echo $modulo['nombreModulo']; ?>
                    </option>
                <?php } ?>
            </select>

            <a href="vistas/retos/agregarRetos.php" class="boton-primario">
                <i class="fas fa-plus"></i> Agregar Reto
            </a>
        </div>
    </div>
</div>

<?php if (!empty($exito)) { ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

<?php if (!empty($error)) { ?>
<div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo $error; ?></p>
</div>
<?php } ?>

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
            <?php if (empty($listaRetos)) { ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay retos registrados</td>
            </tr>
            <?php } else { ?>
                <?php foreach ($listaRetos as $reto) { 
                    $modulosDeReto = obtenerModulosDeReto($reto['idReto']);
                    $nombresModulos = array_column($modulosDeReto, 'nombreModulo');
                    $idsModulos = array_column($modulosDeReto, 'idModulo');
                    $idsCiclos = array_unique(array_column($modulosDeReto, 'idCiclo'));
                ?>
                <tr data-modulos="<?php echo implode(',', $idsModulos); ?>" 
                    data-ciclos="<?php echo implode(',', $idsCiclos); ?>">
                    <td><?php echo $reto['idReto']; ?></td>
                    <td><strong><?php echo $reto['nombreReto']; ?></strong></td>
                    <td><?php echo date('d/m/Y', strtotime($reto['fechaInicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($reto['fechaFin'])); ?></td>
                    <td><span class="estado-bolita contador-neutral"><?php echo $reto['horasReto']; ?> h</span></td>
                    <td>
                        <div class="texto-pequeno texto-atenuado lh-1-4 max-w-250">
                            <?php echo implode(', ', $nombresModulos); ?>
                        </div>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/retos/calificarReto.php?idReto=<?php echo $reto['idReto']; ?>" 
                               class="boton-icono boton-ver" title="Calificar Estudiantes">
                                <i class="fas fa-graduation-cap"></i>
                            </a>
                            <a href="vistas/retos/modificarRetos.php?idReto=<?php echo $reto['idReto']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/retos/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este reto?');">
                                <input type="hidden" name="idReto" value="<?php echo $reto['idReto']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroCiclo = document.getElementById('filtroCiclo');
    const filtroModulo = document.getElementById('filtroModulo');
    const tablaFilas = document.querySelectorAll('#tablaRetos tbody tr');
    const modulosOptions = Array.from(filtroModulo.options);

    function filtrar() {
        const cicloId = filtroCiclo.value;
        const moduloId = filtroModulo.value;

        modulosOptions.forEach(opt => {
            if (opt.value === "") return;
            const optCiclo = opt.getAttribute('data-ciclo');
            if (cicloId === "" || optCiclo === cicloId) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });

        tablaFilas.forEach(fila => {
            if (fila.classList.contains('sin-datos')) return;
            
            const ciclosFila = (fila.getAttribute('data-ciclos') || "").split(',');
            const modulosFila = (fila.getAttribute('data-modulos') || "").split(',');
            
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
        filtroModulo.value = ""; 
        filtrar();
    });
    filtroModulo.addEventListener('change', filtrar);
});
</script>

<?php include '../comunes/footer.php'; ?>
