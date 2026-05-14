# AulaPro - Gestión Académica (TFG)

Este es mi Trabajo de Fin de Grado para el ciclo de Desarrollo de Aplicaciones Web (DAW) en el CPS Ibaiondo.

## ¿De qué va el proyecto?
**AulaPro** es una plataforma para gestionar un centro de FP. La idea es quitarse de encima los Excels y tenerlo todo en una web: alumnos, profes, notas, inventario... todo centralizado.

Lo he hecho usando PHP con arquitectura **MVC** pero "a pelo", sin usar frameworks tipo Laravel para aprender bien cómo funciona todo por debajo.

## Características principales
- **Tres portales:** Uno para Admin, otro para Profesores y otro para Alumnos. Cada uno ve lo que le toca.
- **Gestión de notas:** Calcula automáticamente las notas finales (75% módulos y 25% retos).
- **PDFs:** Genera boletines de notas y certificados usando la librería FPDF.
- **Notificaciones:** Usa Firebase para avisos al móvil y la API de Brevo para mandar las notas por email.
- **Inventario:** Un pequeño módulo para prestar portátiles o tablets a los alumnos.
- **TFG:** Los alumnos pueden subir su proyecto y los profes ponerles la nota ahí mismo.

## Tecnologías usadas
* **Backend:** PHP 8 y MySQL.
* **Frontend:** HTML, CSS (responsive a mano) y algo de JS/jQuery para filtros.
* **APIs:** Firebase Cloud Messaging y Brevo (SMTP).
* **Servidor:** XAMPP.

## Cómo instalarlo
1. Descarga el repo y mételo en la carpeta `htdocs` de XAMPP.
2. Crea una base de datos llamada `pfc` en phpMyAdmin.
3. Importa el archivo `database.sql` que está en la raíz.
4. Configura la conexión en `modelos/conectar.php` (yo lo tengo con root y sin pass).
5. (Opcional) Si quieres que funcionen los correos, mete tu API Key en `config/secrets.php`.

---
**Autor:** Yassin Lahhit
*DAW - CPS Ibaiondo (Bilbao)*
Curso 2025-2026
