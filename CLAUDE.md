# AulaPro – Directrices del Proyecto

Este proyecto está diseñado para un perfil de estudiante de nivel intermedio en PHP (Ciclo DAW). 
El código debe ser **simple, legible y fácil de explicar** en una defensa de TFG. 
No se debe rediseñar la arquitectura ni aplicar patrones avanzados (como Inyección de Dependencias o Middleware) a menos que se solicite expresamente.

## 📝 Reglas de Oro
- **Simplicidad sobre sofisticación:** Es preferible un `if/else` claro que una operación ternaria compleja o un `match`.
- **Estructura MVC Estricta:** Mantener la separación entre `vistas/` (HTML/PHP), `controladores/` (Lógica de flujo y validación) y `modelos/` (Consultas SQL).
- **Persona:** El código debe parecer escrito por un estudiante con talento: limpio y organizado, pero usando herramientas estándar de PHP sin sobre-ingeniería.

## 🏗️ Estructura y Helpers
- **Validación:** Se realiza exclusivamente en los **Controladores**.
- **Consultas SQL:** Se realizan exclusivamente en los **Modelos**.
- **Helpers Existentes:** Utilizar siempre que sea posible:
  - `lib/fpdf/fpdf.php` para PDFs.
  - `controladores/comunes/email_helper.php` para la API de Brevo.
  - `controladores/firebase/firebase_helper.php` para notificaciones push.

## 🛠️ Estándares de Código
- **PHP:** Procedural con funciones organizadas en clases/modelos.
- **Validaciones Permitidas:** `isset()`, `empty()`, `is_numeric()`, `preg_match()`, `trim()`. Evitar `filter_input()`.
- **SQL:** Consultas claras. Usar `JOIN` cuando sea necesario pero evitar subconsultas anidadas o lógica de negocio pesada dentro de la DB.
- **Naming:** Variables en español (preferiblemente) o inglés, pero siempre descriptivas (ej: `$idEstudiante`, `$resultadoNotas`). No usar nombres crípticos como `$s`, `$data1`.
- **Sintaxis:** Usar llaves estándar `{ }`. No usar la sintaxis alternativa de PHP (`endif;`, `endforeach;`).

## 🚫 Prohibido
- Cambiar la estructura de carpetas sin permiso.
- Borrar comentarios existentes que expliquen el flujo.
- Añadir dependencias externas vía Composer (salvo que sea imprescindible).
- Usar frameworks de frontend pesados; mantener CSS nativo y JS/jQuery.

## 💡 Cómo responder
- Proporciona cambios mínimos y quirúrgicos.
- Si detectas un error de seguridad básico (ej: inyección SQL), corrígelo usando `prepared statements` (si ya se están usando en el proyecto) o menciónalo discretamente.
- Al explicar cambios, usa un tono de "compañero de clase avanzado" o "tutor".
