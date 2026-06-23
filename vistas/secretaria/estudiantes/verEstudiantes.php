<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
$estudiantes = listarEstudiantes();

$titulo_pagina = 'AULAPRO | ESTUDIANTES';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>ESTUDIANTES</h1>
    <a href="agregarEstudiantes.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO ESTUDIANTE
    </a>
</div>

<div class="panel margen-abajo">
    <div class="caja">
        <div class="campo relleno">
            <label for="buscarEstudiante">Buscar</label>
            <input type="text" id="buscarEstudiante" placeholder="Nombre, email, DNI...">
        </div>
    </div>
</div>

<div class="panel">
    <?php if (empty($estudiantes)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-user-graduate"></i></div>
            <div class="panel-vacio-titulo">No hay estudiantes</div>
            <div class="panel-vacio-desc">Agrega el primer estudiante para comenzar.</div>
        </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantes">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>DNI</th>
                    <th>Teléfono</th>
                    <th style="width:60px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $est): ?>
                <tr>
                    <td><b><?= Security::escapeHtml($est['nombreEstudiante']) ?></b></td>
                    <td><?= Security::escapeHtml($est['emailEstudiante']) ?></td>
                    <td><?= Security::escapeHtml($est['dniEstudiante'] ?? '—') ?></td>
                    <td><?= Security::escapeHtml($est['telefonoEstudiante'] ?? '—') ?></td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="verDetallesEstudiantes.php?id=<?= (int)$est['idEstudiante'] ?>">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                                <a class="recurso-menu-item" href="modificarEstudiantes.php?id=<?= (int)$est['idEstudiante'] ?>">
                                    <i class="fas fa-pen"></i> Editar
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
filtrarTabla('buscarEstudiante', 'tablaEstudiantes');
iniciarPaginacion('tablaEstudiantes', 15);
</script>
