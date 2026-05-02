$baseDir = "C:\xampp\htdocs\pfc\vistas"
$files = Get-ChildItem -Path $baseDir -Filter *.php -Recurse

foreach ($file in $files) {
    $relativePath = $file.FullName.Substring($baseDir.Length + 1)
    $parts = $relativePath.Split([System.IO.Path]::DirectorySeparatorChar)
    $depth = $parts.Count
    
    $prefix = ""
    for ($i=0; $i -lt $depth; $i++) { $prefix += "../" }
    
    $content = [System.IO.File]::ReadAllText($file.FullName)
    
    # 1. Replace /pfc/ paths
    # Match /pfc/ in attributes
    $content = $content -replace '(["''])/pfc/', ('$1' + $prefix)
    
    # 2. Replace isset ternary
    $content = $content -replace 'isset\(\$(_GET|_POST|_SESSION)\[(["''])(.*?)\2\]\)\s*\?\s*\$(_GET|_POST|_SESSION)\[(["''])\3\5\]\s*:\s*(["''])(.*?)\6', '$$$1[''$3''] ?? ''$7'''

    # 3. Replace if(isset) block with initialization
    $content = $content -replace '\$([a-zA-Z0-9_]+)\s*=\s*(["''])(.*?)\2\s*;\s*if\s*\(isset\(\$(_GET|_POST|_SESSION)\[(["''])(.*?)\5\]\)\)\s*\{\s*\$\1\s*=\s*\$(_GET|_POST|_SESSION)\[(["''])\6\8\]\s*;\s*\}', '$$$1 = $$$4[''$6''] ?? ''$3'';'

    # 4. Handle: if (isset($_GET['id'])) { $id = (int)$_GET['id']; }
    $content = $content -replace 'if\s*\(isset\(\$(_GET|_POST|_SESSION)\[(["''])(.*?)\2\]\)\)\s*\{\s*\$([a-zA-Z0-9_]+)\s*=\s*(\(int\))?\$(_GET|_POST|_SESSION)\[(["''])\3\7\]\s*;\s*\}', '$$$4 = $5($$$1[''$3''] ?? 0);'

    [System.IO.File]::WriteAllText($file.FullName, $content)
}
write-output "Refactor complete for $baseDir"
