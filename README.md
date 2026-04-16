# PFC - Sistema de Gestión Académica

## Descripción del Proyecto
Sistema de gestión académica desarrollado en PHP Native MVC. El proyecto permite gestionar estudiantes, profesores y cursos.

## Estructura del Proyecto
```
pfc/
├── admin/          # Panel de administración principal
│   ├── index.php        # Página principal
│   ├── controladores/     # Controladores MVC
│   ├── modelos/          # Modelos MVC
│   ├── vistas/          # Vistas MVC
│   ├── estiloAdmin/ # CSS
│   └── imagenesSuperAdmin/ # Imágenes
├── profesores/          # Panel de profesores
├── estudiantes/         # Panel de estudiantes
├── landing/             # Página de inicio
├── backup_SQL/          # Copias de seguridad de la base de datos
└── features/           # Documentación de características
```

## Requisitos del Sistema
- PHP 7.4 o superior
- MySQL/MariaDB
- Servidor web (Apache/Nginx)

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/yassinlahhit95/pfc.git
   ```

2. **Configurar la base de datos**
   - Importar uno de los archivos de backup: `backup_SQL_pfc.sql`
   - Configurar los parámetros de conexión en los modelos correspondientes

3. **Configurar el servidor web**
   - Apuntar el DocumentRoot al directorio `pfc/`
   - Asegurar que los permisos estén configurados correctamente

## Uso

### Panel de Administración
- Acceso: `admin/index.php`
- Gestión completa de usuarios, estudiantes y cursos

## Estructura MVC

### Modelos
- `admin/modelos/` - Modelos de datos
- Responsables de la interacción con la base de datos

### Vistas
- `admin/vistas/` - Plantillas HTML
- `admin/estiloAdmin/` - Estilos CSS

### Controladores
- `admin/controladores/` - Lógica de negocio
- Procesan las peticiones y coordinan modelos y vistas

## Características Técnicas

- **PHP Native**: Sin frameworks externos
- **MVC Architecture**: Separación clara de responsabilidades

## Desarrollo

### Estructura de Archivos
- **Controladores**: Gestionan la lógica de negocio
- **Modelos**: Manejan la persistencia de datos
- **Vistas**: Presentan la interfaz de usuario

### Funcionalidades Clave
- Gestión de estudiantes
- Gestión de profesores
- Gestión de cursos
- Sistema de notificaciones
- Navegación dinámica

## Estándares de Codificación

Para mantener la consistencia y seguridad del proyecto, se deben seguir los siguientes estándares:

### Validación de Datos Numéricos
En los controladores, los campos numéricos (IDs, horas, montos, etc.) deben validarse manualmente sin utilizar `filter_var` o `filter_input`. El método estándar es:
```php
if (!is_numeric($valor) || !ctype_digit($valor) || !preg_match('/^[0-9]+$/', $valor)) {
    // Error de validación
}
```
*Nota: Para montos decimales, la regex debe ajustarse (ej: `/^[0-9]+(\.[0-9]{1,2})?$/`).*

### Seguridad y Escapado
- **Prohibido el uso de `htmlentities`**: No se debe utilizar `htmlentities` en ninguna parte del proyecto.
- **Uso de `htmlspecialchars`**: Para la salida de datos en las vistas (evitar XSS), se debe utilizar preferiblemente `htmlspecialchars`.
- **Inputs Numéricos**: Se prefiere el uso de `type="text"` en lugar de `type="number"` en los formularios HTML, delegando la validación estricta al controlador.

## Licencia
Este proyecto está bajo licencia MIT - ver el archivo LICENSE para más detalles.

## Autor
Yassin Lahhit