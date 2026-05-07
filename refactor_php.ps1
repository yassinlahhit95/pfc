$files = Get-ChildItem -Path "vistas" -Filter "*.php" -Recurse

foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName)
    $originalContent = $content

    # if (...):
    $content = [regex]::Replace($content, '<\?php\s+if\s*\((.*?)\)\s*:\s*\?>', '<?php if ($1) { ?>')
    
    # foreach (...):
    $content = [regex]::Replace($content, '<\?php\s+foreach\s*\((.*?)\)\s*:\s*\?>', '<?php foreach ($1) { ?>')
    
    # while (...):
    $content = [regex]::Replace($content, '<\?php\s+while\s*\((.*?)\)\s*:\s*\?>', '<?php while ($1) { ?>')
    
    # for (...):
    $content = [regex]::Replace($content, '<\?php\s+for\s*\((.*?)\)\s*:\s*\?>', '<?php for ($1) { ?>')
    
    # elseif (...):
    $content = [regex]::Replace($content, '<\?php\s+elseif\s*\((.*?)\)\s*:\s*\?>', '<?php } elseif ($1) { ?>')

    # else:
    $content = [regex]::Replace($content, '<\?php\s+else\s*:\s*\?>', '<?php } else { ?>')

    # endif;
    $content = [regex]::Replace($content, '<\?php\s+endif\s*;\s*\?>', '<?php } ?>')

    # endforeach;
    $content = [regex]::Replace($content, '<\?php\s+endforeach\s*;\s*\?>', '<?php } ?>')

    # endwhile;
    $content = [regex]::Replace($content, '<\?php\s+endwhile\s*;\s*\?>', '<?php } ?>')

    # endfor;
    $content = [regex]::Replace($content, '<\?php\s+endfor\s*;\s*\?>', '<?php } ?>')

    if ($content -ne $originalContent) {
        [System.IO.File]::WriteAllText($file.FullName, $content)
        Write-Host "Updated: $($file.FullName)"
    }
}
