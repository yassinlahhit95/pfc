
$replacements = @{
    'Ã³' = 'ó'
    'Ã' = 'Í'
    'Ã©' = 'é'
    'Ã¡' = 'á'
    'Ãº' = 'ú'
    'Ã±' = 'ñ'
    'Â¿' = '¿'
    'â†' = '←'
    'Ã“' = 'Ó'
    'Ã' = 'Á'
    'Ã‰' = 'É'
    'Ãš' = 'Ú'
    'Ã‘' = 'Ñ'
}

$rootPath = "vistas\admin"
$files = Get-ChildItem -Path $rootPath -Recurse -Filter "*.php"

foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
    $changed = $false

    # 1. UTF-8 replacements
    foreach ($old in $replacements.Keys) {
        if ($content.Contains($old)) {
            $content = $content.Replace($old, $replacements[$old])
            $changed = $true
        }
    }

    # 2. Uppercase Section Headers in list views (starting with 'ver')
    if ($file.Name.StartsWith("ver")) {
        $newContent = [regex]::Replace($content, '(?i)(<h1>)(.*?)(</h1>)', {
            param($match)
            $tagOpen = $match.Groups[1].Value
            $h1Content = $match.Groups[2].Value
            $tagClose = $match.Groups[3].Value
            
            # Uppercase ignoring PHP tags
            $parts = [regex]::Split($h1Content, '(<(?:\?|%)[^>]*?(?:\?|%)>)')
            $newH1Content = ""
            foreach ($part in $parts) {
                if ($part -match '^<[\?%]') {
                    $newH1Content += $part
                } else {
                    $newH1Content += $part.ToUpper()
                }
            }
            return "$tagOpen$newH1Content$tagClose"
        })
        if ($newContent -ne $content) {
            $content = $newContent
            $changed = $true
        }
    }

    # 3. Title Variable
    $newContent = [regex]::Replace($content, '(\$titulo_pagina\s*=\s*)"(.*?)"', {
        param($match)
        $prefix = $match.Groups[1].Value
        $value = $match.Groups[2].Value
        
        # Apply UTF-8 fix to value
        $fixedValue = $value
        foreach ($old in $replacements.Keys) {
            $fixedValue = $fixedValue.Replace($old, $replacements[$old])
        }
        
        # Uppercase ignoring PHP tags
        $parts = [regex]::Split($fixedValue, '(<(?:\?|%)[^>]*?(?:\?|%)>)')
        $newValue = ""
        foreach ($part in $parts) {
            if ($part -match '^<[\?%]') {
                $newValue += $part
            } else {
                $newValue += $part.ToUpper()
            }
        }
        return "$($prefix)`"$newValue`""
    })
    if ($newContent -ne $content) {
        $content = $newContent
        $changed = $true
    }

    # 4. Special case for agregarAnuncios.php
    if ($file.Name -eq "agregarAnuncios.php" -and $file.FullName.Contains("anuncios")) {
        if ($content -like "*← volver*" -or $content -like "*← Volver*") {
            $content = $content -replace "← volver", "← VOLVER"
            $content = $content -replace "← Volver", "← VOLVER"
            $changed = $true
        }
    }

    if ($changed) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
        Write-Host "Fixed: $($file.FullName)"
    }
}
