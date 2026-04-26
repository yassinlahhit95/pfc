<?php
session_start();
$titulo_pagina = "Detalles Profesor - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../../modelos/profesores.php";
require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/modulos.php";
require_once "../../../modelos/retos.php";

$id = 0;
if (isset($_GET['idProfesor'])) {
    $id = $_GET['idProfesor'];
} else if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$profesor = obtenerProfesorPorId($id);

if (!$profesor) {
    echo "<div class='mensaje-error'>Profesor no encontrado. ID recibido: $id</div>";
    include '../comunes/footer.php';
    exit;
}

// Obtener datos asignados
$ciclosProfesor = obtenerCiclosDeProfesor($id);
$modulosProfesor = obtenerModulosDeProfesor($id);
$retosProfesor = obtenerRetosDeProfesor($id);
?>

<div class="encabezado-pagina">
    <h1>Ficha del Profesor</h1>
    <a href="/pfc/vistas/admin/profesores/verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="disposicion-flexible separacion-grande">
    <div class="flexible-rellenar">
        <div class="tarjeta-blanca margen-abajo">
            <div class="titulo-tarjeta">
                <h3><i class="fas fa-user-tie"></i> Información General</h3>
            </div>
            <div class="formulario-cuadricula">
                <div class="campo-formulario">
                    <label class="texto-atenuado texto-pequeno">Nombre Completo</label>
                    <p class="texto-negrita"><?php echo $profesor['nombreProfesor']; ?></p>
                </div>
                <div class="campo-formulario">
                    <label class="texto-atenuado texto-pequeno">Email</label>
                    <p class="texto-negrita"><?php echo $profesor['emailProfesor']; ?></p>
                </div>
                <div class="campo-formulario">
                    <label class="texto-atenuado texto-pequeno">Teléfono</label>
                    <p class="texto-negrita"><?php echo $profesor['telefonoProfesor']; ?></p>
                </div>
                <div class="campo-formulario">
                    <label class="texto-atenuado texto-pequeno">DNI</label>
                    <p class="texto-negrita"><?php echo $profesor['dniProfesor']; ?></p>
                </div>
                <div class="campo-formulario campo-ancho-total">
                    <label class="texto-atenuado texto-pequeno">Dirección</label>
                    <p class="texto-negrita"><?php echo $profesor['direccionProfesor']; ?></p>
                </div>
            </div>
        </div>

        <!-- MÓDULOS ASIGNADOS -->
        <div class="tarjeta-blanca">
            <div class="titulo-tarjeta">
                <h3><i class="fas fa-book"></i> Módulos Impartidos</h3>
            </div>
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <th>Abreviatura Ciclo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($modulosProfesor)) { ?>
                            <tr><td colspan="2" class="sin-datos">No tiene módulos asignados</td></tr>
                        <?php } else { ?>
                            <?php foreach ($modulosProfesor as $m) { ?>
                            <tr>
                                <td><strong><?php echo $m['nombreModulo']; ?></strong></td>
                                <td><span class="etiqueta-estado azul"><?php echo $m['abreviaturaCiclo']; ?></span></td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BARRA LATERAL: CICLOS Y RETOS -->
    <div class="ancho-fijo-300">
        <div class="tarjeta-blanca margen-abajo">
            <div class="titulo-tarjeta"><h3><i class="fas fa-layer-group"></i> Ciclos</h3></div>
            <div class="lista-detalles-lateral">
                <?php if (empty($ciclosProfesor)) { ?>
                    <p class="texto-atenuado">Sin ciclos asignados</p>
                <?php } else { ?>
                    <?php foreach ($ciclosProfesor as $c) { ?>
                        <div class="item-detalle-lateral" style="margin-bottom: 8px; padding: 8px; background: #fdffdf; border-radius: 4px;">
                            <strong><?php echo $c['abreviaturaCiclo']; ?></strong><br>
                            <small><?php echo $c['nombreCiclo']; ?></small>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <div class="tarjeta-blanca">
            <div class="titulo-tarjeta"><h3><i class="fas fa-tasks"></i> Retos Activos</h3></div>
            <div class="lista-detalles-lateral">
                <?php if (empty($retosProfesor)) { ?>
                    <p class="texto-atenuado">Sin retos asignados</p>
                <?php } else { ?>
                    <?php foreach ($retosProfesor as $r) { ?>
                        <div class="item-detalle-lateral" style="margin-bottom: 8px; padding: 8px; background: #f4f8ff; border-radius: 4px;">
                            <strong><?php echo $r['nombreReto']; ?></strong><br>
                            <small><?php echo $r['horasReto']; ?> horas</small>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
