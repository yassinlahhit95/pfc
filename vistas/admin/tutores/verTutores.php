<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tutores.php";

$listaTutores = listarTodosLosTutores();

$titulo_pagina = "AULAPRO | GESTIÓN DE TUTORES";
$seccion = 'tutores';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>SISTEMA PARENTAL</h1>
    <div class="acciones-cabecera" style="display:flex;gap:10px;align-items:center;">
        <span class="texto-suave small"><?= count($listaTutores) ?> familias registradas</span>
        <a href="agregarTutor.php" class="boton-primario">
            <i class="fas fa-plus"></i> AGREGAR FAMILIAR
        </a>
    </div>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaTutores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>DNI</th>
                    <th>HIJOS VINCULADOS</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaTutores)) { ?>
                    <tr>
                        <td colspan="6" class="vacio">No hay tutores registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaTutores as $t): 
                        $hijos = listarEstudiantesPorTutor($t['idTutor']);
                    ?>
                    <tr>
                        <td><?= Security::escapeHtml($t['idTutor']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="ava-mini" style="background: var(--bg-2); color: var(--accent); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; border: 1px solid var(--border); font-size: 0.8rem;">
                                    <?= strtoupper(substr($t['nombreTutor'], 0, 1)) ?>
                                </div>
                                <b><?= strtoupper(Security::escapeHtml($t['nombreTutor'])) ?></b>
                            </div>
                        </td>
                        <td><?= Security::escapeHtml($t['dniTutor']) ?></td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <?php if (empty($hijos)): ?>
                                    <span class="badge bg-light text-muted border">Sin vinculación</span>
                                <?php else: ?>
                                    <?php foreach ($hijos as $h): ?>
                                        <span class="small fw-medium"><i class="fas fa-child me-1 opacity-50"></i> <?= Security::escapeHtml($h['nombreEstudiante']) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= Security::escapeHtml($t['emailTutor']) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="verDetallesTutor.php?id=<?= $t['idTutor'] ?>"><i class="fas fa-search"></i> Ver expediente</a>
                                    <a class="recurso-menu-item" href="modificarTutor.php?id=<?= $t['idTutor'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarTutor.php?id=<?= $t['idTutor'] ?>" onclick="return confirm('¿Eliminar este tutor? Se perderán las vinculaciones familiares.')"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaTutores', 10);
</script>
