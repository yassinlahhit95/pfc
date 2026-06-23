<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$mensajes = listarTodosLosMensajes();

$titulo_pagina = "AULAPRO | MENSAJES";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MENSAJES</h1>
</div>

<div class="panel margen-abajo">
    <div class="filtros">
        <input type="text" id="filtroMensajes" class="filtro-input" placeholder="Buscar mensaje...">
    </div>
</div>

<div class="panel">
    <?php if (empty($mensajes)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-envelope-open"></i></div>
        <div class="panel-vacio-titulo">Sin mensajes</div>
        <div class="panel-vacio-desc">No hay mensajes en el sistema.</div>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaMensajes">
            <thead>
                <tr>
                    <th>Asunto</th>
                    <th>De</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mensajes as $msg): ?>
                <tr <?= !$msg['leido'] ? 'style="font-weight:600;"' : '' ?>>
                    <td><?= Security::escapeHtml($msg['asunto'] ?? '(sin asunto)') ?></td>
                    <td>
                        <?php
                        $rol = $msg['emisor_rol'] ?? 'estudiante';
                        $nombre = ($rol === 'estudiante')
                            ? ($msg['nombreEstudiante'] ?? 'Estudiante')
                            : ($msg['nombreProfesor'] ?? 'Profesor');
                        $col = ($rol === 'estudiante') ? 'azul' : 'verde';
                        ?>
                        <span class="texto-estado <?= $col ?>"><?= Security::escapeHtml($nombre) ?></span>
                    </td>
                    <td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($msg['fecha']))) ?></td>
                    <td>
                        <?php if ($msg['leido']): ?>
                        <span class="texto-estado gris">Leído</span>
                        <?php else: ?>
                        <span class="texto-estado azul">Nuevo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="ver.php?id=<?= (int)$msg['idReclamacion'] ?>" class="boton-secundario"
                           style="padding:4px 10px; font-size:0.8rem;">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
filtrarTabla('filtroMensajes', 'tablaMensajes');
iniciarPaginacion('tablaMensajes', 15);
</script>
