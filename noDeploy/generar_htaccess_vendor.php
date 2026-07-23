<?php
// ══════════════════════════════════════════════════════════════════════
// Recrea vendor/.htaccess tras cada `composer install`/`update`.
// vendor/ está en .gitignore (se instala en cada entorno, no se sube por
// FTP), así que un .htaccess creado a mano ahí se pierde en cuanto alguien
// borra y reinstala la carpeta — de ahí el hook post-autoload-dump en
// composer.json, para que esta protección nunca dependa de un paso manual.
// ══════════════════════════════════════════════════════════════════════
$destino = __DIR__ . '/../vendor/.htaccess';

$contenido = <<<'HTACCESS'
# Bloquear acceso directo a este directorio (dependencias de Composer:
# código fuente y manifiestos como installed.json no deben ser públicos).
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;

if (is_dir(__DIR__ . '/../vendor')) {
    file_put_contents($destino, $contenido);
    echo "vendor/.htaccess generado.\n";
}
