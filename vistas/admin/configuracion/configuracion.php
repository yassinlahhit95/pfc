<?php
require_once __DIR__ . '/../../../include/Security.php';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . '/../../../modelos/configuracion.php';
$cfg = obtenerConfiguracionCentro();

$titulo_pagina = "AULAPRO | CONFIGURACIÓN DEL CENTRO";
$seccion = 'configuracion';
include_once __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>CONFIGURACIÓN DEL CENTRO</h1>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php endif; ?>

<form method="POST" action="../../../controladores/admin/configuracion/guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

    <div class="panel margen-abajo">
        <h3 class="panel-titulo">Datos del Centro</h3>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Nombre del Centro *</label>
                <input type="text" name="nombreCentro" value="<?= Security::escapeHtml($cfg['nombreCentro']) ?>" required>
            </div>
            <div class="campo">
                <label>Código del Centro</label>
                <input type="text" name="codigoCentro" value="<?= Security::escapeHtml($cfg['codigoCentro']) ?>">
            </div>
            <div class="campo">
                <label>Curso Escolar</label>
                <input type="text" name="cursoEscolar" value="<?= Security::escapeHtml($cfg['cursoEscolar']) ?>" placeholder="2024-2025">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Dirección</label>
                <input type="text" name="direccionCentro" value="<?= Security::escapeHtml($cfg['direccionCentro']) ?>">
            </div>
            <div class="campo">
                <label>Ciudad</label>
                <input type="text" name="ciudadCentro" value="<?= Security::escapeHtml($cfg['ciudadCentro']) ?>">
            </div>
            <div class="campo">
                <label>Código Postal</label>
                <input type="text" name="cpCentro" value="<?= Security::escapeHtml($cfg['cpCentro']) ?>">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Teléfono</label>
                <input type="text" name="telefonoCentro" value="<?= Security::escapeHtml($cfg['telefonoCentro']) ?>">
            </div>
            <div class="campo relleno">
                <label>Email del Centro</label>
                <input type="email" name="emailCentro" value="<?= Security::escapeHtml($cfg['emailCentro']) ?>">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Nombre Director/a Firmante</label>
                <input type="text" name="nombreDirectorFirmante" value="<?= Security::escapeHtml($cfg['nombreDirectorFirmante']) ?>">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Texto Legal / Pie de Documento</label>
                <textarea name="textoLegal" rows="3"><?= Security::escapeHtml($cfg['textoLegal']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="panel margen-abajo">
        <h3 class="panel-titulo">Logotipos (PNG/JPG, máx. 2MB)</h3>
        <div class="caja caja-libre espacio-grande">

            <?php foreach ([
                'logoCentro'    => 'Logo del Centro (izquierda)',
                'logoGobierno1' => 'Logo Gobierno / Ministerio (derecha)',
                'logoGobierno2' => 'Logo Consejería / Junta (centro-derecha)',
            ] as $field => $label): ?>
            <div class="campo relleno cfg-logo-campo">
                <label><?= $label ?></label>
                <?php if (!empty($cfg[$field])): ?>
                    <img src="../../../public/uploads/configuracion/<?= Security::escapeHtml(basename($cfg[$field])) ?>"
                         alt="logo" class="cfg-logo-preview">
                <?php endif; ?>
                <input type="file" name="<?= $field ?>" accept="image/*">
                <small class="texto-suave">Deja vacío para mantener el actual</small>
            </div>
            <?php endforeach; ?>

        </div>
    </div>

    <div class="acciones">
        <button type="submit" class="boton-primario">
            <i class="fas fa-save"></i> GUARDAR CONFIGURACIÓN
        </button>
    </div>
</form>

<style>
.cfg-logo-preview { display:block; max-height:80px; max-width:200px; object-fit:contain; margin-bottom:8px; border:1px solid #e5e7eb; border-radius:6px; padding:4px; background:#f9fafb; }
.panel-titulo { font-size:.85rem; font-weight:700; letter-spacing:.05em; color:#6b7280; margin-bottom:16px; padding-bottom:8px; border-bottom:1px solid #e5e7eb; }
</style>

<?php include '../comunes/footer.php'; ?>
