import os

def fix_file(file_path, replacements):
    if not os.path.exists(file_path):
        print(f"File not found: {file_path}")
        return
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    for old, new in replacements:
        content = content.replace(old, new)
    
    with open(file_path, 'w', encoding='utf-8', newline='\n') as f:
        f.write(content)
    print(f"Fixed: {file_path}")

# File 1: vistas/admin/estudiantes/verEstudiantes.php
fix_file(r'C:\xampp\htdocs\pfc\vistas\admin\estudiantes\verEstudiantes.php', [
    ('CORREO ELECTRÃ“NICO', 'CORREO ELECTRÓNICO'),
    ('$titulo_pagina = "AULAPRO | GESTIÓN DE ESTUDIANTES";', '$titulo_pagina = "AULAPRO | LISTADO DE ESTUDIANTES";')
])

# File 2: vistas/admin/profesores/verProfesores.php
fix_file(r'C:\xampp\htdocs\pfc\vistas\admin\profesores\verProfesores.php', [
    ('GESTIÃƑÂ€ŒN', 'GESTIÓN'),
    ('CORREO ELECTRÃƒâ€œNICO', 'CORREO ELECTRÓNICO'),
    ('MÃƒÂ³dulos especÃƒÂ­ficos', 'Módulos específicos'),
    ("confirm('Ã‚Â¿EstÃƒÂ¡s", "confirm('¿Estás"),
    ('$titulo_pagina = "AULAPRO | GESTIÓN DE PROFESORES";', '$titulo_pagina = "AULAPRO | PROFESORES DEL CENTRO";')
])

# File 3: vistas/admin/modulos/verModulos.php
fix_file(r'C:\xampp\htdocs\pfc\vistas\admin\modulos\verModulos.php', [
    ('GESTIÃ“N DE MÃ“DULOS', 'GESTIÓN DE MÓDULOS'),
    ('$titulo_pagina = "AULAPRO | GESTIÓN DE MÓDULOS";', '$titulo_pagina = "AULAPRO | MÓDULOS PROFESIONALES";')
])

# File 4: vistas/admin/ciclos/verCiclos.php
fix_file(r'C:\xampp\htdocs\pfc\vistas\admin\ciclos\verCiclos.php', [
    ('$titulo_pagina = "AULAPRO | GESTIÓN DE CICLOS";', '$titulo_pagina = "AULAPRO | CICLOS FORMATIVOS";')
])
