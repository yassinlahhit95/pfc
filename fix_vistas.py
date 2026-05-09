import os
import re

mangled_chars = {
    'Ã³': 'ó', 'Ã­': 'í', 'Ã©': 'é', 'Ã¡': 'á', 'Ãº': 'ú', 'Ã±': 'ñ',
    'Ã“': 'Ó', 'Ã': 'Í', 'Ã‰': 'É', 'Ã': 'Á', 'Ãš': 'Ú', 'Ã‘': 'Ñ',
    'Â¿': '¿', 'â†': '←', 'â‚¬': '€', 'Âª': 'ª'
}

def fix_content(content, file_path):
    # 1. Fix mangled characters
    for mangled, fixed in mangled_chars.items():
        content = content.replace(mangled, fixed)
    
    # 2. Standardize Browser Titles
    # Match $titulo_pagina = "..." or $tituloDelPagina = "..."
    def title_replacer(match):
        var_name = match.group(1)
        title_val = match.group(2)
        # Remove 'AULAPRO | ' if it exists (case insensitive)
        clean_title = re.sub(r'^AULAPRO\s*\|\s*', '', title_val, flags=re.IGNORECASE)
        new_title = f"AULAPRO | {clean_title.upper()}"
        return f'{var_name} = "{new_title}"'

    content = re.sub(r'(\$(?:titulo_pagina|tituloDelPagina))\s*=\s*"([^"]*)"', title_replacer, content)
    content = re.sub(r"(\$(?:titulo_pagina|tituloDelPagina))\s*=\s*'([^']*)'", 
                     lambda m: f'{m.group(1)} = "AULAPRO | {re.sub(r"^AULAPRO\s*\|\s*", "", m.group(2), flags=re.IGNORECASE).upper()}"', 
                     content)

    # 3. Standardize Section Headers <h1>
    def h1_replacer(match):
        attributes = match.group(1) or ""
        inner = match.group(2)
        # Uppercase only literal parts, but not inside PHP tags
        parts = re.split(r'(<\?.*?\?>)', inner, flags=re.DOTALL)
        new_parts = []
        for part in parts:
            if part.startswith('<?'):
                new_parts.append(part)
            else:
                new_parts.append(part.upper())
        return f'<h1{attributes}>{"".join(new_parts)}</h1>'

    content = re.sub(r'<h1([^>]*)>(.*?)</h1>', h1_replacer, content, flags=re.DOTALL | re.IGNORECASE)

    # 4. Specific page fixes
    if 'vistas/admin/pagos/verPagosGeneral.php' in file_path.replace('\\', '/'):
        # Ensure 'GESTIÓN DE PAGOS', 'PRÓXIMO PAGO', 'PAGO ÚNICO', and '€' are correct
        # Most should be handled by mangled_chars and uppercase <h1>
        pass
    
    if 'vistas/admin/academico/calificacionesModulos.php' in file_path.replace('\\', '/'):
        # Fix 'NOTAS DE MÓDULOS' and table headers like '1ª Ev'
        # '1Âª Ev' -> '1ª Ev' (handled by mangled_chars)
        pass

    return content

def main():
    vistas_dir = 'vistas'
    for root, dirs, files in os.walk(vistas_dir):
        for file in files:
            if file.endswith('.php'):
                file_path = os.path.join(root, file)
                try:
                    with open(file_path, 'r', encoding='utf-8') as f:
                        content = f.read()
                    
                    new_content = fix_content(content, file_path)
                    
                    if new_content != content:
                        with open(file_path, 'w', encoding='utf-8', newline='') as f:
                            f.write(new_content)
                        print(f"Fixed: {file_path}")
                except Exception as e:
                    print(f"Error processing {file_path}: {e}")

if __name__ == "__main__":
    main()
