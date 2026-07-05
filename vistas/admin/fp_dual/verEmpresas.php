<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";

FeatureGuard::requirePage('feature_fp_dual');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/fp_dual.php";

$empresas = listarEmpresas();

$titulo_pagina = "AULAPRO | EMPRESAS FP DUAL";
$seccion = 'fp_dual';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>EMPRESAS COLABORADORAS (FP DUAL)</h1>
    </div>
    <div class="acciones-pagina">
        <a href="agregarEmpresa.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVA EMPRESA
        </a>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEmpresas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE DE LA EMPRESA</th>
                    <th>CIF</th>
                    <th>CONTACTO</th>
                    <th>TELÉFONO</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empresas)) { ?>
                    <tr>
                        <td colspan="7" class="vacio">No hay empresas registradas en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($empresas as $empresa) { ?>
                    <tr>
                        <td><?= Security::escapeHtml($empresa['idEmpresa']) ?></td>
                        <td><b><?= Security::escapeHtml(mb_strtoupper($empresa['nombre'], 'UTF-8')) ?></b></td>
                        <td><?= Security::escapeHtml($empresa['cif']) ?></td>
                        <td>
                            <div style="font-size: 13px; font-weight: 500;"><?= Security::escapeHtml($empresa['persona_contacto']) ?></div>
                            <div class="texto-pequeno texto-suave"><?= Security::escapeHtml($empresa['email']) ?></div>
                        </td>
                        <td><?= Security::escapeHtml($empresa['telefono']) ?></td>
                        <td>
                            <?php if ($empresa['activo']) { ?>
                                <span class="texto-estado verde">Activa</span>
                            <?php } else { ?>
                                <span class="texto-estado gris">Inactiva</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="editarEmpresa.php?idEmpresa=<?= Security::escapeHtml($empresa['idEmpresa']) ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$empresa['idEmpresa'] ?>"
                                       data-tipo="Empresa"
                                       data-nombre="<?= Security::escapeHtml($empresa['nombre']) ?>"
                                       data-url="/controladores/admin/fp_dual/borrar.php"
                                       data-campo="idEmpresa"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaEmpresas', 15);
</script>
