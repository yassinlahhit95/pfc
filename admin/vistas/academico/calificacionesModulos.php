<?php
session_start();
$titulo_pagina = "Notas por Módulo - Super Admin";
$seccion = 'notas_modulos';
include_once "../comunes/nav.php";

require_once "../../modelos/ciclos.php";
require_once "../../modelos/modulos.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/calificaciones.php";

$listaCiclos = listarTodosLosCiclos();
$idCicloElegido = $_GET['idCiclo'] ?? null;
$idModuloElegido = $_GET['idModulo'] ?? null;

// Obtenemos modulos (filtrados por ciclo si hay uno elegido)
$listaModulos = [];
if ($idCicloElegido) {
    // Solo mostramos los modulos de ese ciclo
    $todosLosModulos = listarModulos();
    foreach ($todosLosModulos as $m) {
        if ($m['idCiclo'] == $idCicloElegido) {
            $listaModulos[] = $m;
        }
    }
} else {
    // Si no hay ciclo, mostramos todos
    $listaModulos = listarModulos();
}

$estudiantesDelModulo = [];
if ($idModuloElegido) {
    // Buscamos el ciclo del modulo elegido para estar seguros
    $datosModuloInfo = null;
    $todosM = listarModulos();
    foreach($todosM as $tm) {
        if ($tm['idModulo'] == $idModuloElegido) {
            $datosModuloInfo = $tm;
            break;
        }
    }
    
    if ($datosModuloInfo) {
        $idCicloDelMod = $datosModuloInfo['idCiclo'];
        // Listar estudiantes de ese ciclo
        $listaTodos = listarEstudiantes();
        foreach ($listaTodos as $e) {
            if ($e['idCiclo'] == $idCicloDelMod) {
                $estudiantesDelModulo[] = $e;
            }
        }
    }
}

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Calificaciones por Asignatura</h1>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><p><?php echo $exito; ?></p></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><p><?php echo $error; ?></p></div>
<?php } ?>

<!-- Selectores de Filtro -->
<div class="tarjeta-blanca">
    <form method="GET" class="formulario-cuadricula alinear-centro">
        
        <!-- 1. Filtrar por Ciclo -->
        <div class="campo-formulario">
            <label>1. Filtrar por Ciclo</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaCiclos as $c) { ?>
                    <option value="<?php echo $c['idCiclo']; ?>" <?php if ($idCicloElegido == $c['idCiclo']) echo 'selected'; ?>>
                        <?php echo $c['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- 2. Seleccionar Módulo -->
        <div class="campo-formulario">
            <label>2. Seleccionar Módulo</label>
            <select name="idModulo" onchange="this.form.submit()">
                <option value="">-- Elige un módulo --</option>
                <?php foreach ($listaModulos as $m) { ?>
                    <option value="<?php echo $m['idModulo']; ?>" <?php if ($idModuloElegido == $m['idModulo']) echo 'selected'; ?>>
                        <?php echo $m['nombreModulo']; ?> <?php if (!$idCicloElegido) echo "(" . $m['nombreCiclo'] . ")"; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mt-25">
            <?php if ($idCicloElegido || $idModuloElegido) { ?>
                <a href="vistas/academico/calificacionesModulos.php" class="boton-secundario">Limpiar Filtros</a>
            <?php } ?>
        </div>
    </form>
</div>

<?php if ($idModuloElegido) { ?>
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Lista de Clase</h3>
    </div>
    <form action="controladores/academico/calificarModulos.php" method="POST">
        <input type="hidden" name="idModulo" value="<?php echo $idModuloElegido; ?>">
        
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th style="text-align: center;">1ª EV</th>
                        <th style="text-align: center;">1ª FINAL</th>
                        <th style="text-align: center;">2ª EV</th>
                        <th style="text-align: center;">2ª FINAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($estudiantesDelModulo)) { ?>
                        <tr><td colspan="5" class="sin-datos">No hay estudiantes registrados en este ciclo.</td></tr>
                    <?php } else { ?>
                        <?php foreach ($estudiantesDelModulo as $est) { 
                            $notas = obtenerNotasModulo($est['idEstudiante'], $idModuloElegido);
                        ?>
                        <tr>
                            <td><strong><?php echo $est['nombreEstudiante']; ?></strong></td>
                            
                            <td style="text-align: center;">
                                <input type="text" name="n1ev[<?php echo $est['idEstudiante']; ?>]" 
                                       value="<?php echo $notas['nota_1ev'] ?? ''; ?>" 
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>

                            <td style="text-align: center;">
                                <input type="text" name="n1f[<?php echo $est['idEstudiante']; ?>]" 
                                       value="<?php echo $notas['nota_1final'] ?? ''; ?>" 
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>

                            <td style="text-align: center;">
                                <input type="text" name="n2ev[<?php echo $est['idEstudiante']; ?>]" 
                                       value="<?php echo $notas['nota_2ev'] ?? ''; ?>" 
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>

                            <td style="text-align: center;">
                                <input type="text" name="n2f[<?php echo $est['idEstudiante']; ?>]" 
                                       value="<?php echo $notas['nota_2final'] ?? ''; ?>" 
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarNotas" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Calificaciones
            </button>
        </div>
    </form>
</div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
