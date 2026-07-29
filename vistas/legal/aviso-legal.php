<?php
declare(strict_types=1);

$legal_titulo = 'Aviso Legal';
$legal_pagina = 'aviso-legal';
require __DIR__ . '/_header.php';

$lang = I18n::getLang();
$langFile = __DIR__ . "/lang/{$legal_pagina}_{$lang}.php";
if (file_exists($langFile)) {
    include $langFile;
} else {
    include __DIR__ . "/lang/{$legal_pagina}_es.php";
}

require __DIR__ . '/_footer.php';
