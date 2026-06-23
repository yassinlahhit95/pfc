<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/inventario.php";

$prestamos = listarTodosLosPrestamos();

$titulo_pagina = "AULAPRO | GESTIÓN DE PRÉSTAMOS";
$seccion = 'prestamos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>PRÉSTAMOS</h1>
    <a href="agregarPrestamo.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO PRÉSTAMO</a>
    <a href="verInventario.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <?php if (empty($prestamos)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-hand-holding"></i></div>
        <div class="panel-vacio-titulo">Sin préstamos registrados</div>
        <div class="panel-vacio-desc">No hay préstamos en el sistema.</div>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaPrestamos">
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th>Estudiante</th>
                    <th>Fecha préstamo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $p): ?>
                <tr>
                    <td><?= Security::escapeHtml($p['nombreArticulo'] ?? '—') ?></td>
                    <td><?= Security::escapeHtml($p['nombreEstudiante'] ?? '—') ?></td>
                    <td><?= Security::escapeHtml(date('d/m/Y', strtotime($p['fechaPrestamo']))) ?></td>
                    <td>
                        <?php
                        $est = $p['estadoPrestamo'] ?? 'en curso';
                        $col = ($est === 'devuelto') ? 'verde' : 'naranja';
                        ?>
                        <span class="texto-estado <?= $col ?>"><?= Security::escapeHtml(ucfirst($est)) ?></span>
                    </td>
                    <td>
                        <?php if (($p['estadoPrestamo'] ?? '') !== 'devuelto'): ?>
                        <form method="POST" action="../../../controladores/secretaria/inventario/devolver.php"
                              style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="idPrestamo" value="<?= (int)$p['idPrestamo'] ?>">
                            <button type="submit" class="boton-secundario" style="padding:4px 10px; font-size:0.8rem;">
                                <i class="fas fa-rotate-left"></i> Devolver
                            </button>
                        </form>
                        <?php else: ?>
                        <span class="texto-suave">—</span>
                        <?php endif; ?>
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
iniciarPaginacion('tablaPrestamos', 15);
</script>
