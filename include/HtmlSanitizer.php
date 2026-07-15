<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Sanitiza HTML generado por el editor de texto enriquecido (bold, títulos,
// listas, enlaces, imágenes y vídeo embebido de YouTube/Vimeo) antes de guardarlo,
// para evitar XSS almacenado ya que el contenido se muestra sin escapar en el blog público.
class HtmlSanitizer {
    public static function clean(string $html): string {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,h1,h2,h3,h4,h5,h6,ul,ol,li,a[href|target|rel],img[src|alt],blockquote,span[style],iframe[src|width|height|allow|allowfullscreen|frameborder]');
        $config->set('CSS.AllowedProperties', ['color', 'background-color']);
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
        $config->set('HTML.TargetBlank', true);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('Cache.SerializerPath', sys_get_temp_dir());
        $config->set('HTML.DefinitionID', 'aulapro-blog-editor');
        $config->set('HTML.DefinitionRev', 1);

        $def = $config->maybeGetRawHTMLDefinition();
        if ($def !== null) {
            $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
            $def->addAttribute('iframe', 'allow', 'Text');
        }

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }
}
