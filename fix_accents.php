<?php
$file = 'vistas/estudiantes/pfc/subir.php';
$content = file_get_contents($file);
$content = str_replace('GESTIN', 'GESTIÓN', $content);
$content = str_replace('Ests seguro', '¿Estás seguro', $content);
$content = str_replace('INTEGRACIN', 'INTEGRACIÓN', $content);
file_put_contents($file, $content);
echo 'Fixed accents';
