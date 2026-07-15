<?php
// Redimensiona y recomprime imágenes subidas (landing, blog) para reducir
// el peso servido a visitantes públicos de las páginas de landing. No toca
// vídeo. Se degrada de forma segura si la extensión GD no está disponible:
// deja el archivo original tal cual.
class ImageOptimizer
{
    public static function optimize(string $path, string $mime, int $maxEdge = 1920, int $quality = 82): void
    {
        if (!function_exists('gd_info')) return;

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

        // Se escribe a un fichero temporal y solo se sustituye el original si
        // el resultado es realmente más pequeño (o si hubo redimensionado,
        // que siempre conviene aunque el ahorro en bytes sea pequeño). PNG es
        // sin pérdida: el nivel de compresión (0-9) solo cambia el esfuerzo de
        // compresión, nunca la calidad visual, así que se usa siempre el máximo.
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

        if (is_file($tmpPath) && ($wasResized || filesize($tmpPath) < filesize($path))) {
            rename($tmpPath, $path);
        } elseif (is_file($tmpPath)) {
            unlink($tmpPath);
        }
    }
}
