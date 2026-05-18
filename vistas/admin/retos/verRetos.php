<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$todos_los_retos = listarRetos();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();
$mapaCicloNivel = [];
foreach ($listaDeCiclosParaFiltro as $cicloFiltro) {
    $mapaCicloNivel[$cicloFiltro['idCiclo']] = $cicloFiltro['idNivel'];
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "AULAPRO | GESTIÓN DE RETOS";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>RETOS / PROYECTOS</h1>
    <a href="agregarRetos.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO RETO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel margen-abajo">
    <div class="d-flex envoltura-flexible sep-g">
        <div class="campo relleno">
            <label>FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivel" onchange="aplicarFiltrosRetos()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                    <option value="<?= $nivelFiltro['idNivel'] ?>">
                        <?= $nivelFiltro['nombreNivel'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label>FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="aplicarFiltrosRetos()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= $cicloFiltro['idCiclo'] ?>">
                        <?= $cicloFiltro['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="panel">
    <div class="tcont">
        <table class="tabla-datos" id="tablaRetos">
            <thead>
                <tr>
                    <th>Nombre del Reto</th>
                    <th>Módulos</th>
                    <th>Horas</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_retos)) { ?>
                    <tr><td colspan="6" class="vacio">No hay retos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_retos as $reto) {
                        $modulos = listarModulosDeReto($reto['idReto']);
                        $nombresModulos = array_column($modulos, 'nombreModulo');
                        $textoModulos = !empty($nombresModulos) ? implode(", ", $nombresModulos) : "<em>Sin módulos</em>";
                        $idCicloReto = !empty($modulos) ? $modulos[0]['idCiclo'] : '';
                    ?>
                    <tr class="fila-ciclo-<?= $idCicloReto ?> fila-nivel-<?= $mapaCicloNivel[$idCicloReto] ?? '' ?>">
                        <td><b><?= $reto['nombreReto'] ?></b></td>
                        <td><?= $textoModulos ?></td>
                        <td><?= $reto['horasReto'] ?>h</td>
                        <td><?= date('d/m/Y', strtotime($reto['fechaInicio'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($reto['fechaFin'])) ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarRetos.php?idReto=<?= $reto['idReto'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/retos/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar este reto?')">
                                    <input type="hidden" name="idReto" value="<?= $reto['idReto'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
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
<script>
function aplicarFiltrosRetos() {
    var idNivel = document.getElementById('selectFiltroNivel').value;
    var idCiclo = document.getElementById('selectFiltroCiclo').value;
    var filas = document.querySelectorAll('#tablaRetos tbody tr');

    filas.forEach(function(fila) {
        var pasaNivel = true;
        var pasaCiclo = true;

        if (idNivel !== '') {
            pasaNivel = fila.classList.contains('fila-nivel-' + idNivel);
        }
        if (idCiclo !== '') {
            pasaCiclo = fila.classList.contains('fila-ciclo-' + idCiclo);
        }

        if (pasaNivel && pasaCiclo) {
            fila.classList.remove('fila-filtro-oculta');
        } else {
            fila.classList.add('fila-filtro-oculta');
        }
    });
}
</script>




