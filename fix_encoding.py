import os
import re

replacements = {
    'Ã³': 'ó',
    'Ã': 'Í',
    'Ã©': 'é',
    'Ã¡': 'á',
    'Ãº': 'ú',
    'Ã±': 'ñ',
    'Â¿': '¿',
    'â†': '←',
    'Ã“': 'Ó',
    'Ã': 'Á',
    'Ã‰': 'É',
    'Ãš': 'Ú',
    'Ã‘': 'Ñ',
    'Ã­': 'í',
    'Ã³': 'ó'
}

def uppercase_ignoring_php(text):
    # Split by PHP tags
    parts = re.split(r'(<(?:\?|%)[^>]*?(?:\?|%)>)', text)
    new_parts = []
    for part in parts:
        if part.startswith('<') and ('?' in part or '%' in part):
            new_parts.append(part)
        else:
            new_parts.append(part.upper())
    return "".join(new_parts)

def fix_content(content, filename):
    # 1. UTF-8 replacements
    for old, new in replacements.items():
        content = content.replace(old, new)
    
    basename = os.path.basename(filename)
    
    # 2. Uppercase Section Headers in list views (starting with 'ver')
    if basename.startswith('ver') and basename.endswith('.php'):
        def uppercase_h1(match):
            tag_open = match.group(1)
            h1_content = match.group(2)
            tag_close = match.group(3)
            return f'{tag_open}{uppercase_ignoring_php(h1_content)}{tag_close}'
        
        content = re.sub(r'(<h1>)(.*?)(</h1>)', uppercase_h1, content, flags=re.IGNORECASE | re.DOTALL)

    # 3. Title Variable
    def fix_titulo(match):
        prefix = match.group(1)
        value = match.group(2)
        # Apply UTF-8 fix to value just in case
        fixed_value = value
        for old, new in replacements.items():
            fixed_value = fixed_value.replace(old, new)
        return f'{prefix}"{uppercase_ignoring_php(fixed_value)}"'

    content = re.sub(r'(\$titulo_pagina\s*=\s*)"(.*?)"', fix_titulo, content)
    
    # 4. Special case for agregarAnuncios.php
    if basename == 'agregarAnuncios.php' and 'anuncios' in filename:
        # The requirement: ensure the Volver button is inside the '.encabezado-pagina' div next to the <h1> title and says '← VOLVER' (uppercase).
        # We saw it was already like that, but let's make sure text is uppercase.
        content = content.replace('← volver', '← VOLVER')
        content = content.replace('← Volver', '← VOLVER')
        # If it wasn't inside .encabezado-pagina, we'd need more complex logic, but it is.

    return content

root_dir = r'C:\xampp\htdocs\pfc\vistas\admin'

for root, dirs, files in os.walk(root_dir):
    for file in files:
        if file.endswith('.php'):
            file_path = os.path.join(root, file)
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
            except UnicodeDecodeError:
                with open(file_path, 'r', encoding='latin-1') as f:
                    content = f.read()
            
            new_content = fix_content(content, file_path)
            
            if new_content != content:
                with open(file_path, 'w', encoding='utf-8', newline='') as f:
                    f.write(new_content)
                print(f"Fixed: {file_path}")
