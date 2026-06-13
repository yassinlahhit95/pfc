<?php
require_once 'session_config.php';
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header("Location: panel_gestion.php");
    exit;
}

if (isset($_POST['fase'])) {
    $fase = intval($_POST['fase']);
    if ($fase >= 1 && $fase <= 4) {
        $file = '../admision_denegada.php';
        if (is_writable($file)) {
            $content = file_get_contents($file);
            
            // Flexible regex to match the line regardless of minor spacing differences
            $pattern = '/\$fase\s*=\s*isset\(\$_GET\[\'fase\'\]\)\s*\?\s*intval\(\$_GET\[\'fase\'\]\)\s*:\s*\d+;/';
            $replacement = '$fase = isset($_GET[\'fase\']) ? intval($_GET[\'fase\']) : ' . $fase . ';';
            
            $new_content = preg_replace($pattern, $replacement, $content);
            
            if ($new_content !== null) {
                if (file_put_contents($file, $new_content) !== false) {
                     header("Location: panel_gestion.php?status=success_fase");
                     exit;
                } else {
                     header("Location: panel_gestion.php?status=error_write");
                     exit;
                }
            } else {
                header("Location: panel_gestion.php?status=error_regex");
                exit;
            }
        } else {
            header("Location: panel_gestion.php?status=error_permissions");
            exit;
        }
    }
}

header("Location: panel_gestion.php");
exit;
?>
