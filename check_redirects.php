<?php
$dir = new RecursiveDirectoryIterator('controladores');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    if (preg_match_all('/header\s*\(\s*["\']Location:\s*([^"\']+)["\']\s*\)/i', $content, $matches)) {
        foreach ($matches[1] as $location) {
            if (strpos($location, 'http') === 0 || strpos($location, '#') === 0 || $location === '') continue;
            
            // Extract path without query parameters
            $parsed_url = parse_url($location);
            $action = $parsed_url['path'] ?? $location;
            
            if (strpos($action, '/') === 0) {
                $abs_path = realpath(__DIR__ . $action);
            } else {
                $abs_path = realpath(dirname($path) . '/' . $action);
            }

            if (!$abs_path || !file_exists($abs_path)) {
                echo "Broken redirect in $path: $location\n";
            }
        }
    }
}
echo "Done checking redirects in controladores.\n";
