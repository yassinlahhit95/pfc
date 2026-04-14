# PFC - Sistema de Gestión Académica

## Descripción del Proyecto
Sistema de gestión académica desarrollado en PHP Native MVC con funcionalidad offline. El proyecto permite gestionar estudiantes, profesores y cursos con capacidad de funcionamiento sin conexión a internet.

## Características Principales

### Funcionalidad Offline
- **Almacenamiento Local**: Uso de LocalStorage para guardar datos de estudiantes y cursos en el navegador
- **Gestión de Inserción Offline**: Cuando se añade un nuevo estudiante sin conexión, los datos se almacenan en un array dentro de LocalStorage
- **Sincronización Automática**: Una vez recuperada la conexión, los datos pendientes se envían automáticamente al controlador mediante AJAX (jQuery) usando peticiones POST estándar
- **Actualización de Cache**: Cuando hay conexión, se actualiza el LocalStorage con los últimos datos de la base de datos MySQL

### Estructura del Proyecto
```
pfc/
├── superAdmin/          # Panel de administración principal
│   ├── index.php        # Página principal
│   ├── controlador/     # Controladores MVC
│   ├── modelo/          # Modelos MVC
│   ├── vistas/          # Vistas MVC
│   ├── js/              # JavaScript
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
- Navegador web moderno con soporte para LocalStorage
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
- Acceso: `superAdmin/index.php`
- Gestión completa de usuarios, estudiantes y cursos
- Funcionalidad offline disponible

### Funcionalidad Offline
El sistema detecta automáticamente el estado de conexión:
- **Sin conexión**: Los datos se almacenan localmente
- **Con conexión**: Los datos se sincronizan automáticamente

## Estructura MVC

### Modelos
- `superAdmin/modelo/` - Modelos de datos
- Responsables de la interacción con la base de datos

### Vistas
- `superAdmin/vistas/` - Plantillas HTML
- `superAdmin/estiloAdmin/` - Estilos CSS
- `superAdmin/js/` - Funcionalidades JavaScript

### Controladores
- `superAdmin/controlador/` - Lógica de negocio
- Procesan las peticiones y coordinan modelos y vistas

## Características Técnicas

- **PHP Native**: Sin frameworks externos
- **MVC Architecture**: Separación clara de responsabilidades
- **Offline First**: Diseño centrado en funcionamiento sin conexión
- **AJAX Synchronization**: Sincronización asíncrona de datos
- **LocalStorage**: Almacenamiento persistente en el navegador

## Desarrollo

### Estructura de Archivos
- **Controladores**: Gestionan la lógica de negocio
- **Modelos**: Manejan la persistencia de datos
- **Vistas**: Presentan la interfaz de usuario
- **JavaScript**: Implementan funcionalidad offline

### Funcionalidades Clave
- Gestión de estudiantes
- Gestión de profesores
- Gestión de cursos
- Sistema de notificaciones
- Navegación dinámica

## Licencia
Este proyecto está bajo licencia MIT - ver el archivo LICENSE para más detalles.

## Autor
Yassin Lahhit