<?php
// Redimensiona y recomprime imágenes subidas en cualquier punto de la app
// (landing, blog, recursos de aula, comprobantes de pago, justificantes...)
// para reducir el peso servido. No toca vídeo. Se degrada de forma segura
// si la extensión GD no está disponible: deja el archivo original tal cual.
class ImageOptimizer
{
    public static function optimize(string $path, string $mime, int $maxEdge = 1920, int $quality = 82): void
    {
        if (!function_exists('gd_info')) return;

        // ponytail: GIF deliberately excluded — GD's imagegif() only ever writes a
        // single frame, so routing animated GIFs through this optimizer would
        // silently destroy their animation. Uploaded GIFs are therefore stored
        // as-is (not re-encoded), which means any trailing polyglot payload past
        // valid GIF data isn't stripped for that one format. Low risk in practice
        // (no known GIF-polyglot-to-script-execution vector under a correct
        // image/gif Content-Type in modern browsers) — upgrade path if this ever
        // needs closing: re-encode only static (single-frame) GIFs and leave
        // multi-frame ones untouched.
        $creators = [
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png'  => 'imagecreatefrompng',
            'image/webp' => 'imagecreatefromwebp',
        ];
        if (!isset($creators[$mime]) || !function_exists($creators[$mime])) return;

        $src = @$creators[$mime]($path);
        if (!$src) return;

        $width  = imagesx($src);
        $height = imagesy($src);
        $longEdge = max($width, $height);
        $wasResized = $longEdge > $maxEdge;

        if ($wasResized) {
            $scale = $maxEdge / $longEdge;
            $newW  = max(1, (int)round($width * $scale));
            $newH  = max(1, (int)round($height * $scale));
            $resized = imagecreatetruecolor($newW, $newH);
            if ($mime === 'image/png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }
            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($src);
            $src = $resized;
        }

        // Se escribe a un fichero temporal y SIEMPRE se sustituye el original por
        // el resultado re-codificado (nunca se compara tamaño): el propósito de este
        // paso es normalizar el fichero — decodificar y re-encodear con GD descarta
        // cualquier byte que no forme parte de los datos de imagen válidos (la
        // técnica clásica de "polyglot": payload arbitrario anexado tras el final
        // de una imagen válida). Comparar tamaños y quedarse con el original cuando
        // "no compensaba" dejaba ese payload intacto. PNG es sin pérdida: el nivel
        // de compresión (0-9) solo cambia el esfuerzo de compresión, nunca la
        // calidad visual, así que se usa siempre el máximo.
        $tmpPath = $path . '.opt_tmp';
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($src, $tmpPath, $quality);
                break;
            case 'image/png':
                imagepng($src, $tmpPath, 9);
                break;
            case 'image/webp':
                if (!function_exists('imagewebp')) { imagedestroy($src); return; }
                imagewebp($src, $tmpPath, $quality);
                break;
        }
        imagedestroy($src);

        if (is_file($tmpPath)) {
            rename($tmpPath, $path);
        }
    }
}
