<?php
session_start();
$titulo_pagina = "AULAPRO | CICLOS FORMATIVOS";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$todos_los_ciclos = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>CICLOS FORMATIVOS</h1>
    <a href="agregarCiclos.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO CICLO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label>FILTRAR POR NIVEL:</label>
        <select id="selectFiltroNivel" onchange="filtrarTabla('selectFiltroNivel', 'tablaCiclos')">
            <option value="">-- Todos los Niveles --</option>
            <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                <option value="<?= $nivelFiltro['nombreNivel'] ?>">
                    <?= $nivelFiltro['nombreNivel'] ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaCiclos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE DEL CICLO</th>
                    <th>NIVEL</th>
                    <th>TUTORES/PROFESORES</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_ciclos)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay ciclos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_ciclos as $ciclo) { 
                        $nombresTutores = obtenerNombresTutoresCiclo($ciclo['idCiclo']);
                        $textoTutores = !empty($nombresTutores) ? implode(", ", $nombresTutores) : '<span class="texto-atenuado">Sin asignar</span>';
                    ?>
                    <tr>
                        <td><?= $ciclo['idCiclo'] ?></td>
                        <td><strong><?= $ciclo['nombreCiclo'] ?></strong></td>
                        <td><?= $ciclo['nombreNivel'] ?></td>
                        <td><?= $textoTutores ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarCiclos.php?idCiclo=<?= $ciclo['idCiclo'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/ciclos/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar este ciclo?')">
                                    <input type="hidden" name="idCiclo" value="<?= $ciclo['idCiclo'] ?>">
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




