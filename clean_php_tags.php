<?php
$dirs = ['controladores', 'modelos', 'config'];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveDirectoryIterator($dir);
    $ite = new RecursiveIteratorIterator($iterator);
    $files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

    foreach($files as $file) {
        $path = $file[0];
        $content = file_get_contents($path);
        
        // Remove closing PHP tag and trailing whitespace at the very end of the file
        $new_content = preg_replace('/\?>\s*$/', '', $content);
        
        if ($new_content !== $content) {
            file_put_contents($path, $new_content);
            echo "Removed closing tag from $path\n";
        }
    }
}
echo "Done cleaning PHP tags.\n";
