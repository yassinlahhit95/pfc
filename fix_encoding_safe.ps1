
$C3 = [char]0x00C3
$C2 = [char]0x00C2
$E2 = [char]0x00E2

$replacements = @{
    "$($C3)$([char]0x00B3)" = [char]0x00F3 # ó
    "$($C3)$([char]0x008D)" = [char]0x00CD # Í
    "$($C3)$([char]0x00A9)" = [char]0x00E9 # é
    "$($C3)$([char]0x00A1)" = [char]0x00E1 # á
    "$($C3)$([char]0x00BA)" = [char]0x00FA # ú
    "$($C3)$([char]0x00B1)" = [char]0x00F1 # ñ
    "$($C2)$([char]0x00BF)" = [char]0x00BF # ¿
    "$($E2)$([char]0x0086)$([char]0x0090)" = [char]0x2190 # ←
    "$($C3)$([char]0x0093)" = [char]0x00D3 # Ó
    "$($C3)$([char]0x0081)" = [char]0x00C1 # Á
    "$($C3)$([char]0x0089)" = [char]0x00C9 # É
    "$($C3)$([char]0x009A)" = [char]0x00DA # Ú
    "$($C3)$([char]0x0091)" = [char]0x00D1 # Ñ
    "$($C3)$([char]0x00AD)" = [char]0x00ED # í
}

$rootPath = "vistas\admin"
$files = Get-ChildItem -Path $rootPath -Recurse -Filter "*.php"

foreach ($file in $files) {
    # Read as UTF8 but allow PowerShell to handle the string
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
        $volverText = "$([char]0x2190) VOLVER"
        if ($content -notmatch [regex]::Escape($volverText)) {
             # Replace any variation of volver with uppercase and arrow
             $content = [regex]::Replace($content, '←\s*volver', $volverText, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
             $changed = $true
        }
    }

    if ($changed) {
        # Use UTF8 without BOM by using a specialized encoding object if needed,
        # but standard UTF8 in Set-Content might add BOM.
        # To avoid BOM:
        $utf8NoBOM = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBOM)
        Write-Host "Fixed: $($file.FullName)"
    }
}
