<?php
// ══════════════════════════════════════════════════════════════════════
// Se ejecuta tras `composer install`/`update` (ver composer.json), igual
// que generar_htaccess_vendor.php — un script plano sin dependencias de
// la app, porque en este punto puede que .env todavía no exista. Revisa
// los requisitos del servidor y, si la instalación no está completada
// todavía, indica el siguiente paso (visitar /install/).
// ══════════════════════════════════════════════════════════════════════

$requeridas = ['mysqli', 'zip', 'curl', 'openssl', 'mbstring', 'fileinfo'];
$faltan = array_filter($requeridas, fn($ext) => !extension_loaded($ext));

echo "\n";
echo "─────────────────────────────────────────────────────────\n";
echo " AulaPro — comprobación post-instalación\n";
echo "─────────────────────────────────────────────────────────\n";

echo version_compare(PHP_VERSION, '8.3.0', '>=')
    ? " ✓ PHP " . PHP_VERSION . "\n"
    : " ✕ PHP " . PHP_VERSION . " — se requiere 8.3 o superior\n";

foreach ($requeridas as $ext) {
    echo extension_loaded($ext) ? " ✓ Extensión $ext\n" : " ✕ Extensión $ext — falta, instálala antes de continuar\n";
}

if ($faltan) {
    echo "\n ⚠ Faltan extensiones obligatorias: " . implode(', ', $faltan) . "\n";
    echo "   Instálalas y vuelve a ejecutar `composer install`.\n";
} elseif (is_file(__DIR__ . '/../install/.installed')) {
    echo "\n ✓ Instalación ya completada — nada más que hacer.\n";
} else {
    echo "\n → Siguiente paso: visita /install/ en tu navegador para completar la instalación guiada.\n";
    echo "   (o, sin asistente: copia .env.example a .env, importa noDeploy/database.sql\n";
    echo "   y noDeploy/seed_minimal.sql a mano — ver README.md)\n";
}
echo "─────────────────────────────────────────────────────────\n\n";
