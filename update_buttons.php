<?php
$files = [
    'vistas/admin/estudiantes/modificarEstudiantes.php',
    'vistas/admin/retos/agregarRetos.php',
    'vistas/admin/retos/modificarRetos.php',
    'vistas/admin/directores/agregarDirectores.php',
    'vistas/admin/directores/modificarDirectores.php',
    'vistas/admin/profesores/modificarProfesores.php',
    'vistas/admin/directores/perfil.php',
    'vistas/admin/estudiantes/agregarEstudiantes.php',
    'vistas/admin/ciclos/modificarCiclos.php',
    'vistas/admin/modulos/modificarModulos.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        $patterns = [
            '/<div class="margen-arriba disposicion-flexible separacion-media">/' => '<div class="form-estandar-botones">',
            '/<div class="margen-arriba disposicion-flexible" style="justify-content: flex-end; gap: 15px;">/' => '<div class="form-estandar-botones">',
            '/<div class="margen-arriba">/' => '<div class="form-estandar-botones">',
            '/<div class="form-acciones">/' => '<div class="form-estandar-botones">'
        ];
        
        $new_content = preg_replace(array_keys($patterns), array_values($patterns), $content);
        
        if ($new_content !== $content) {
            file_put_contents($file, $new_content);
            echo "Updated buttons in $file\n";
        } else {
            echo "No changes needed for buttons in $file\n";
        }
    }
}
echo "Done replacing button containers.\n";
