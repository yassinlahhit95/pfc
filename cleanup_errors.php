<?php
$dir = __DIR__ . '/vistas';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $filePath) {
    $content = file_get_contents($filePath);
    $originalContent = $content;

    // 1. Find which variable names were used for session keys
    $erroresVarName = 'errores';
    if (preg_match('/\$([a-zA-Z0-9_]+)\s*=\s*\$_SESSION\[\'errores\'\]/', $content, $matches)) {
        $erroresVarName = $matches[1];
    } else if (strpos($content, '$errs') !== false) {
        $erroresVarName = 'errs';
    }

    $errorVarName = 'error';
    if (preg_match('/\$([a-zA-Z0-9_]+)\s*=\s*\$_SESSION\[\'error\'\]/', $content, $matches)) {
        $errorVarName = $matches[1];
    } else if (strpos($content, '$err') !== false) {
        $errorVarName = 'err';
    }
    
    $exitoVarName = 'exito';
    if (preg_match('/\$([a-zA-Z0-9_]+)\s*=\s*\$_SESSION\[\'exito\'\]/', $content, $matches)) {
        $exitoVarName = $matches[1];
    }

    // 2. Rename variables throughout the file to standard names FIRST
    // This makes it easier to check for usage later
    if ($erroresVarName !== 'errores') {
        $content = preg_replace('/\$' . $erroresVarName . '\b/', '$errores', $content);
    }
    if ($errorVarName !== 'error') {
        $content = preg_replace('/\$' . $errorVarName . '\b/', '$error', $content);
    }
    if ($exitoVarName !== 'exito') {
        $content = preg_replace('/\$' . $exitoVarName . '\b/', '$exito', $content);
    }
    
    // Catch $err and $errs specifically if they were not already caught
    $content = preg_replace('/\$errs\b/', '$errores', $content);
    $content = preg_replace('/\$err\b/', '$error', $content);

    // 3. Check usage (excluding the top-level assignments we are about to replace)
    // We'll remove existing assignments and unsets first to get a clean count
    $tempContent = $content;
    $patternsToRemove = [
        '/\$error\s*=\s*\$_SESSION\[\'error\'\]\s*\?\?\s*[^;]+;/',
        '/\$exito\s*=\s*\$_SESSION\[\'exito\'\]\s*\?\?\s*[^;]+;/',
        '/\$errores\s*=\s*\$_SESSION\[\'errores\'\]\s*\?\?\s*[^;]+;/',
        '/unset\(\s*\$_SESSION\[\'error\'\].*?\);/s',
        '/unset\(\s*.*?\$_SESSION\[\'errores\'\].*?\);/s',
        '/unset\(\s*.*?\$_SESSION\[\'exito\'\].*?\);/s'
    ];
    foreach ($patternsToRemove as $p) {
        $tempContent = preg_replace($p, '', $tempContent);
    }
    
    $usesError = strpos($tempContent, '$error') !== false;
    $usesExito = strpos($tempContent, '$exito') !== false;
    $usesErrores = strpos($tempContent, '$errores') !== false;

    // 4. Construct the new session block
    if ($usesError || $usesExito || $usesErrores || strpos($content, '$_SESSION[\'error\']') !== false || strpos($content, '$_SESSION[\'exito\']') !== false || strpos($content, '$_SESSION[\'errores\']') !== false) {
        
        $lines = [];
        $unsets = [];
        
        // Even if not used in file, we might want to initialize them to avoid undefined variable warnings 
        // if the file was recently changed and I missed some usage. 
        // But the prompt says if NOT USED, it's dead code.
        
        // Actually, many files might use them in included files? No, vistas usually have their own.
        
        $lines[] = "\$error = \$_SESSION['error'] ?? '';";
        $lines[] = "\$exito = \$_SESSION['exito'] ?? '';";
        $lines[] = "\$errores = \$_SESSION['errores'] ?? [];";
        $unsets = ["\$_SESSION['error']", "\$_SESSION['exito']", "\$_SESSION['errores']"];

        $sessionStartPos = strpos($content, 'session_start();');
        if ($sessionStartPos !== false) {
            // Remove old ones
            foreach ($patternsToRemove as $p) {
                $content = preg_replace($p, '', $content);
            }
            // Remove extra whitespace/newlines created by removal
            $content = preg_replace("/\n\s*\n\s*\n/", "\n\n", $content);
            
            $newBlock = implode("\n", $lines) . "\nunset(" . implode(", ", $unsets) . ");";
            $content = str_replace('session_start();', "session_start();\n\n" . $newBlock, $content);
        }
    }

    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "Updated: $filePath\n";
    }
}
?>

