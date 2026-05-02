$targetDirs = "modelos", "controladores", "vistas"
$modifiedCount = 0
$modifiedFiles = New-Object System.Collections.Generic.List[string]

foreach ($dir in $targetDirs) {
    $files = Get-ChildItem -Path $dir -Recurse -Include *.php
    foreach ($file in $files) {
        $content = [System.IO.File]::ReadAllText($file.FullName)
        $originalContent = $content
        
        # Rename $db to $con
        $content = $content -replace '\$db\b', '$con'
        
        # Paths
        $content = $content -replace '(?<!__DIR__\s*\.\s*|\.\s*)(require_once|include_once)\s*\("((?!\/|http|__DIR__|dirname)[^"]+)"\)\s*;', '$1 __DIR__ . "/$2";'
        $content = $content -replace "(?<!__DIR__\s*\.\s*|\.\s*)(require_once|include_once)\s*\('((?!\/|http|__DIR__|dirname)[^']+)'\)\s*;", '$1 __DIR__ . "/$2";'
        $content = $content -replace '(?<!__DIR__\s*\.\s*|\.\s*)(require_once|include_once)\s*"((?!\/|http|__DIR__|dirname)[^"]+)"\s*;', '$1 __DIR__ . "/$2";'
        $content = $content -replace "(?<!__DIR__\s*\.\s*|\.\s*)(require_once|include_once)\s*'((?!\/|http|__DIR__|dirname)[^']+)'\s*;", '$1 __DIR__ . "/$2";'

        # array() to []
        for ($i=0; $i -lt 5; $i++) {
            $content = $content -replace '(?<![a-zA-Z0-9_])array\(([^()]*)\)', '[$1]'
        }
        
        if ($content -ne $originalContent) {
            [System.IO.File]::WriteAllText($file.FullName, $content)
            $modifiedCount++
            $modifiedFiles.Add($file.FullName)
        }
    }
}
Write-Output "Total modified: $modifiedCount"
foreach ($f in $modifiedFiles) {
    $f.Replace((Get-Location).Path + "\", "")
}
