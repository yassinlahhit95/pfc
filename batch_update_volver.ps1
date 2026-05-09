# Regex to match:
# 1. Optional existing font-awesome arrow-left icon
# 2. Optional character-based arrows (including mangled ones)
# 3. The word "Volver" (case-insensitive)
$pattern = '(?i)(<i class="[^"]*fa-arrow-left[^"]*"></i>\s*)?([^a-zA-Z0-9\s<>]|&larr;|&#8592;|&#x2190;)*\s*\bVolver\b'
$replacement = '<i class="fas fa-arrow-left"></i> VOLVER'

$utf8NoBom = New-Object System.Text.UTF8Encoding($false)

Get-ChildItem -Path "vistas" -Filter *.php -Recurse | ForEach-Object {
    $filePath = $_.FullName
    try {
        $content = [System.IO.File]::ReadAllText($filePath)
        
        if ($content -match $pattern) {
            $newContent = $content -replace $pattern, $replacement
            if ($newContent -ne $content) {
                [System.IO.File]::WriteAllText($filePath, $newContent, $utf8NoBom)
                Write-Host "Updated: $filePath"
            }
        }
    } catch {
        Write-Host "Error processing ${filePath}: $($_.Exception.Message)"
    }
}
