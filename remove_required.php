<?php
$dir = new RecursiveDirectoryIterator('vistas');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    
    // Replace "required" or "required='required'" or 'required="required"'
    // We need to be careful not to replace it if it's inside a string that isn't a tag attribute, 
    // but a regex on tags is safer.
    
    $new_content = preg_replace_callback('/<(input|select|textarea)([^>]*?)>/is', function($matches) {
        $tag = $matches[1];
        $attrs = $matches[2];
        // Remove ' required' or ' required="required"' etc
        $attrs = preg_replace('/\s+required(=[\'"]required[\'"])?/i', '', $attrs);
        return "<$tag$attrs>";
    }, $content);
    
    if ($new_content !== $content) {
        file_put_contents($path, $new_content);
        echo "Removed required from $path\n";
        $count++;
    }
}
echo "Removed from $count files.\n";
