$vistasPath = "vistas"
$files = Get-ChildItem -Path $vistasPath -Filter *.php -Recurse

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    if ($content -match '\$titulo(?:_pagina|DelPagina)\s*=\s*') {
        $newContent = [regex]::Replace($content, '(\$titulo(?:_pagina|DelPagina)\s*=\s*)(["''])(.*?)(["'']);', {
            param($match)
            $prefix = $match.Groups[1].Value
            $quote = $match.Groups[2].Value
            $title = $match.Groups[3].Value
            
            # Remove suffixes
            $cleanTitle = $title
            $suffixes = @(" - Admin", " - Estudiante", " - Profesor", " - Portal Profesores", " - Portal Estudiantes", " - Administración", " - Yassin Lahhit")
            
            # Sort suffixes by length descending to avoid partial matches
            $sortedSuffixes = $suffixes | Sort-Object -Descending -Property Length

            foreach ($suffix in $sortedSuffixes) {
                if ($cleanTitle.ToLower().EndsWith($suffix.ToLower())) {
                    $cleanTitle = $cleanTitle.Substring(0, $cleanTitle.Length - $suffix.Length)
                    break
                }
            }
            
            $upperTitle = $cleanTitle.ToUpper().Trim()
            $finalTitle = "AULAPRO | " + $upperTitle
            
            return $prefix + $quote + $finalTitle + $quote + ";"
        })
        $newContent | Set-Content $file.FullName -Encoding UTF8
    }
}
