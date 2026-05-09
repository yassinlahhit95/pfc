$vistasPath = "vistas"
$files = Get-ChildItem -Path $vistasPath -Filter *.php -Recurse
$utf8NoBOM = New-Object System.Text.UTF8Encoding($false)

foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName)
    if ($content -match '\$titulo(?:_pagina|DelPagina)\s*=\s*') {
        $newContent = [regex]::Replace($content, '(\$titulo(?:_pagina|DelPagina)\s*=\s*)(["''])(.*?)(["'']);', {
            param($match)
            $prefix = $match.Groups[1].Value
            $quote = $match.Groups[2].Value
            $title = $match.Groups[3].Value
            
            # Remove existing "AULAPRO | " if any to avoid duplicates
            if ($title.StartsWith("AULAPRO | ")) {
                $title = $title.Substring(10)
            }
            
            # Remove suffixes
            $cleanTitle = $title
            $suffixes = @(" - Admin", " - Estudiante", " - Profesor", " - Portal Profesores", " - Portal Estudiantes", " - Administración", " - Yassin Lahhit", " - AdministraciÃ³n")
            
            # Sort suffixes by length descending to avoid partial matches
            $sortedSuffixes = $suffixes | Sort-Object -Descending -Property Length

            foreach ($suffix in $sortedSuffixes) {
                if ($cleanTitle.ToLower().EndsWith($suffix.ToLower())) {
                    $cleanTitle = $cleanTitle.Substring(0, $cleanTitle.Length - $suffix.Length)
                    break
                }
            }
            
            # Handle the mangled characters if they already exist from previous run
            $cleanTitle = $cleanTitle -replace "GESTIÃ³N", "GESTIÓN"
            $cleanTitle = $cleanTitle -replace "MÃ³DULOS", "MÓDULOS"
            $cleanTitle = $cleanTitle -replace "GESTIÃ“N", "GESTIÓN"
            $cleanTitle = $cleanTitle -replace "MÃ“DULOS", "MÓDULOS"
            $cleanTitle = $cleanTitle -replace "RECLAMACIÃ³N", "RECLAMACIÓN"
            $cleanTitle = $cleanTitle -replace "MENSAJERÃ­A", "MENSAJERÍA"
            $cleanTitle = $cleanTitle -replace "PRÃ©STAMO", "PRÉSTAMO"
            $cleanTitle = $cleanTitle -replace "ARTÃCULO", "ARTÍCULO"
            $cleanTitle = $cleanTitle -replace "ARTÃ­CULO", "ARTÍCULO"
            $cleanTitle = $cleanTitle -replace "AÃ‘ADIR", "AÑADIR"

            $upperTitle = $cleanTitle.ToUpper().Trim()
            $finalTitle = "AULAPRO | " + $upperTitle
            
            return $prefix + $quote + $finalTitle + $quote + ";"
        })
        [System.IO.File]::WriteAllText($file.FullName, $newContent, $utf8NoBOM)
    }
}
