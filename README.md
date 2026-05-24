# Proyecto TFG - AulaPro

Este es mi Proyecto de Fin de Grado. Se trata de una plataforma para gestionar centros de formación, enfocada sobre todo en ciclos de FP.

### Qué hace el programa
El sistema gestiona todo lo que necesita un centro: matricular alumnos, dar de alta profesores y crear los ciclos/módulos. Lo más importante es el sistema de notas, que calcula la media final usando un 75% de los módulos y un 25% de los retos (proyectos).

También tiene:
- Control de pagos mensuales de los alumnos.
- Préstamos de material (inventario).
- Subida de TFG para corrección.
- Notificaciones push con Firebase y correos con Brevo.
- Chat interno y avisos globales.

### Detalles técnicos
He usado PHP nativo con la librería mysqli para la base de datos. Para el diseño he usado CSS puro (sin frameworks como Bootstrap) y la fuente Gilroy. También hay bastante jQuery para los filtros de las tablas y la validación de formularios.

### Cómo instalarlo
1. Importa el `database.sql` en tu MySQL local.
2. Mira el archivo `modelos/conectar.php` para poner tus datos de usuario y contraseña de la base de datos.
3. El archivo `config/secrets.php` no está subido por seguridad (ahí van las claves de las APIs de Firebase y Brevo).

---
Autor: Yassin Lahhit
Año: 2026
