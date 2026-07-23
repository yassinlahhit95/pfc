<?php
// Devuelve la URL (con cache-busting ?v=filemtime) de la versión minificada
// de un asset si existe (generada por `npm run build:assets`), si no la del
// original sin minificar — para que un despliegue sin build funcione igual.
//
// Uso: AssetMin::url(__DIR__, '../../../public/css/features/horario-admin.css')
// $relHref es exactamente el href/src tal cual ya se escribía antes (misma
// profundidad relativa que el resto del proyecto) — esta función no cambia
// esa convención, solo decide entre el archivo y su hermano .min.
class AssetMin {
    public static function url(string $viewDir, string $relHref): string {
        $minHref = preg_replace('/\.(css|js)$/', '.min.$1', $relHref);
        $minAbs  = $viewDir . '/' . $minHref;
        $chosenHref = is_file($minAbs) ? $minHref : $relHref;
        $chosenAbs  = $viewDir . '/' . $chosenHref;
        $v = @filemtime($chosenAbs);
        return $chosenHref . ($v ? '?v=' . $v : '');
    }

    // Igual que url(), pero para hrefs raíz-absolutos (p. ej. "/public/css/x.css",
    // usados por las páginas públicas de vistas/legal/). $docRoot es la ruta de
    // filesystem al directorio que hace de raíz web (padre de public/).
    public static function urlAbs(string $docRoot, string $absHref): string {
        $minHref = preg_replace('/\.(css|js)$/', '.min.$1', $absHref);
        $minAbs  = $docRoot . $minHref;
        $chosenHref = is_file($minAbs) ? $minHref : $absHref;
        $chosenAbs  = $docRoot . $chosenHref;
        $v = @filemtime($chosenAbs);
        return $chosenHref . ($v ? '?v=' . $v : '');
    }
}
