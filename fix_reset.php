<?php
$dir = new RecursiveDirectoryIterator('vistas');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    
    // Regular expression to find <button type="reset" ...> and replace it
    $pattern = '/<button\s+type="reset"([^>]*)>/i';
    $replacement = '<button type="button"$1 onclick="window.location.href = window.location.pathname + window.location.search;">';
    
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
        file_put_contents($path, $content);
        echo "Updated $path\n";
    }
}
echo "Done.\n";
