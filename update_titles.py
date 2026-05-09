import os
import re

def process_files(vistas_path):
    # Regex to find the title definitions
    title_re = re.compile(r'(\$titulo(?:_pagina|DelPagina)\s*=\s*)(["\'])(.*?)(["\']);')
    
    suffixes = [
        " - Admin", " - Estudiante", " - Profesor", 
        " - Portal Profesores", " - Portal Estudiantes", 
        " - Administración", " - Administraci\u00f3n",
        " - Yassin Lahhit"
    ]

    for root, dirs, files in os.walk(vistas_path):
        for file in files:
            if file.endswith('.php'):
                file_path = os.path.join(root, file)
                
                # Try reading with different encodings
                content = None
                for encoding in ['utf-8', 'latin-1', 'windows-1252']:
                    try:
                        with open(file_path, 'r', encoding=encoding) as f:
                            content = f.read()
                        break
                    except UnicodeDecodeError:
                        continue
                
                if content is None:
                    print(f"Could not read file {file_path}")
                    continue

                if '$titulo_pagina' in content or '$tituloDelPagina' in content:
                    def replace_func(match):
                        prefix = match.group(1)
                        quote = match.group(2)
                        title = match.group(3)
                        
                        # Remove existing AULAPRO | if any
                        if title.startswith("AULAPRO | "):
                            title = title[10:]
                        
                        clean_title = title
                        for suffix in suffixes:
                            if clean_title.lower().endswith(suffix.lower()):
                                clean_title = clean_title[:-len(suffix)]
                                break
                        
                        # Convert to uppercase
                        upper_title = clean_title.upper().strip()
                        return f"{prefix}{quote}AULAPRO | {upper_title}{quote};"

                    new_content = title_re.sub(replace_func, content)
                    
                    if new_content != content:
                        # Write back as UTF-8 without BOM
                        with open(file_path, 'w', encoding='utf-8', newline='') as f:
                            f.write(new_content)
                        print(f"Updated {file_path}")

if __name__ == "__main__":
    process_files('vistas')
