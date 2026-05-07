$utf8NoBOM = New-Object System.Text.UTF8Encoding($false)
$arrow = [char]0x2190
$regex = '(?si)<a\s+[^>]*?href="(?<href>[^"]*)"[^>]*?>(?:(?!</a>).)*?\bVolver\b(?:(?!</a>).)*?</a>'
$files = Get-ChildItem -Path "vistas" -Filter "*.php" -Recurse
foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName)
    $newContent = [regex]::Replace($content, $regex, {
        param($m)
        $href = $m.Groups['href'].Value
        $classMatch = [regex]::Match($m.Value, 'class="(?<class>[^"]*)"')
        if ($classMatch.Success) {
            $classes = $classMatch.Groups['class'].Value.Split(" ", [System.StringSplitOptions]::RemoveEmptyEntries)
            $otherClasses = @()
            foreach ($c in $classes) {
                if ($c -ne "boton-secundario") {
                    $otherClasses += $c
                }
            }
            $newClasses = @("boton-secundario") + $otherClasses
            $classStr = $newClasses -join " "
            return "<a href=""$href"" class=""$classStr"">$arrow Volver</a>"
        } else {
            return "<a href=""$href"" class=""boton-secundario"">$arrow Volver</a>"
        }
    })
    [System.IO.File]::WriteAllText($file.FullName, $newContent, $utf8NoBOM)
}
