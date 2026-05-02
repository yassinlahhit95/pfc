<?php
$dir = new RecursiveDirectoryIterator('vistas');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    if (preg_match_all('/<form[^>]+action=["\']([^"\']+)["\']/', $content, $matches)) {
        foreach ($matches[1] as $action) {
            if (strpos($action, 'http') === 0 || strpos($action, '#') === 0 || $action === '') continue;
            
            // Handle absolute paths starting with /
            if (strpos($action, '/') === 0) {
                $abs_path = realpath(__DIR__ . $action);
            } else {
                $abs_path = realpath(dirname($path) . '/' . $action);
            }

            if (!$abs_path || !file_exists($abs_path)) {
                echo "Broken form action in $path: $action\n";
            }
        }
    }
}
echo "Done checking forms.\n";
