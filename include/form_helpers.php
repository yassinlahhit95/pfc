<?php
if (!function_exists('fieldError')) {
    function fieldError($errores, $campo) {
        if (is_array($errores) && !empty($errores[$campo])) {
            return '<span class="campo-error"><i class="fas fa-exclamation-circle"></i> '
                 . Security::escapeHtml($errores[$campo]) . '</span>';
        }
        return '';
    }
    function fieldClass($errores, $campo) {
        return (is_array($errores) && !empty($errores[$campo])) ? ' campo-invalido' : '';
    }
}
