<?php
$css = file_get_contents('public/css/admin.css');
if (strpos($css, '.form-estandar-botones') === false) {
    $css .= "\n\n/* ============================================
   ESTILOS BOTONES FORMULARIO COLUMNA
   ============================================ */
.form-estandar-botones {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    width: 100%;
}

.form-estandar-botones button {
    flex: 1;
    justify-content: center;
}
";
    file_put_contents('public/css/admin.css', $css);
    echo "Added .form-estandar-botones to admin.css\n";
} else {
    echo "Already exists\n";
}
