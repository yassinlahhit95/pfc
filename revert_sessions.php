<?php
function revert_sessions($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $pattern = '/require_once __DIR__ \. "\/(\.\.\/)*config\/session\.php";/';
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, 'session_start();', $content);
                file_put_contents($path, $newContent);
                echo "Updated: $path\n";
            }
        }
    }
}

revert_sessions('vistas');
revert_sessions('controladores');
?>
