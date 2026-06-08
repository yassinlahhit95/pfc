<?php
// Expects $cfg (from obtenerConfiguracionCentro()) to be defined before including this file
$logo1 = logoParaPdf($cfg['logoGobierno1']);
$logo2 = logoParaPdf($cfg['logoGobierno2']);
$logoC = logoParaPdf($cfg['logoCentro']);
?>
<table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:3px solid #1e3a6e; padding-bottom:10px; margin-bottom:14px;">
    <tr>
        <td width="18%" align="left" valign="middle">
            <?php if ($logo1): ?>
                <img src="<?= $logo1 ?>" style="max-height:60px; max-width:110px;">
            <?php else: ?>
                <div style="width:110px; height:60px; background:#f3f4f6; border:1px dashed #d1d5db; border-radius:4px;"></div>
            <?php endif; ?>
        </td>
        <td width="64%" align="center" valign="middle" style="padding:0 10px;">
            <div style="font-size:8pt; color:#6b7280; letter-spacing:.06em; text-transform:uppercase;">Consejería de Educación</div>
            <div style="font-size:14pt; font-weight:700; color:#1e3a6e; margin:3px 0;"><?= htmlspecialchars($cfg['nombreCentro']) ?></div>
            <?php if ($cfg['codigoCentro']): ?>
                <div style="font-size:8pt; color:#6b7280;">Código: <?= htmlspecialchars($cfg['codigoCentro']) ?></div>
            <?php endif; ?>
            <?php if ($cfg['direccionCentro']): ?>
                <div style="font-size:8pt; color:#6b7280;">
                    <?= htmlspecialchars($cfg['direccionCentro']) ?>
                    <?php if ($cfg['ciudadCentro']): ?> — <?= htmlspecialchars($cfg['ciudadCentro']) ?><?php endif; ?>
                    <?php if ($cfg['telefonoCentro']): ?> | Tel: <?= htmlspecialchars($cfg['telefonoCentro']) ?><?php endif; ?>
                </div>
            <?php endif; ?>
        </td>
        <td width="18%" align="right" valign="middle">
            <?php if ($logoC ?: $logo2): ?>
                <img src="<?= $logoC ?: $logo2 ?>" style="max-height:60px; max-width:110px;">
            <?php else: ?>
                <div style="width:110px; height:60px; background:#f3f4f6; border:1px dashed #d1d5db; border-radius:4px; float:right;"></div>
            <?php endif; ?>
        </td>
    </tr>
</table>
