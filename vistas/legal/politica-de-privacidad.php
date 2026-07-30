<?php
declare(strict_types=1);
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
Security::initSession();

$legal_titulo = 'Política de Privacidad';
$legal_pagina = 'politica-de-privacidad';
require __DIR__ . '/_header.php';

$lang = I18n::getLang();
$langFile = __DIR__ . "/lang/{$legal_pagina}_{$lang}.php";
if (file_exists($langFile)) {
    include $langFile;
} else {
    include __DIR__ . "/lang/{$legal_pagina}_es.php";
}

require __DIR__ . '/_footer.php';
