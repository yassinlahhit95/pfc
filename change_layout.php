<?php
$files = [
    'vistas/admin/estudiantes/modificarEstudiantes.php',
    'vistas/admin/retos/agregarRetos.php',
    'vistas/admin/retos/modificarRetos.php',
    'vistas/admin/directores/agregarDirectores.php',
    'vistas/admin/directores/modificarDirectores.php',
    'vistas/admin/profesores/modificarProfesores.php',
    'vistas/admin/directores/perfil.php',
    'vistas/admin/estudiantes/agregarEstudiantes.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $new_content = str_replace('class="formulario-cuadricula"', 'class="form-estandar"', $content);
        if ($new_content !== $content) {
            file_put_contents($file, $new_content);
            echo "Updated $file\n";
        } else {
            echo "No changes needed in $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
echo "Done.\n";
