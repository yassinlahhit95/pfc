# Guía de Redacción de la Memoria del TFG - DAW

Esta guía proporciona la estructura y el contenido recomendado para elaborar la memoria oficial de tu Trabajo de Fin de Grado.

## 1. INTRODUCCIÓN Y OBJETIVOS
*¿De qué trata el proyecto y por qué es importante?*

- **Presentación:** Describe AulaPro como una solución integral para centros de FP.
- **Justificación:** Explica el problema de la dispersión de datos en Excels y la necesidad de una plataforma centralizada.
- **Objetivos:**
    - Digitalizar la gestión de notas (módulos y retos).
    - Automatizar la generación de boletines PDF.
    - Mejorar la comunicación vía push y email.
    - Centralizar la gestión de inventario y pagos.

## 2. ANÁLISIS DEL SISTEMA
*¿Qué necesita el sistema para funcionar?*

- **Requisitos Funcionales:**
    - Gestión multirrol (Admin, Profe, Alumno).
    - Sistema de evaluación ABP (75% módulos, 25% retos).
    - Módulo de préstamos de dispositivos.
    - Buzón de reclamaciones y anuncios.
- **Requisitos No Funcionales:**
    - Interfaz responsive para móviles y tablets.
    - Seguridad en el acceso mediante contraseñas cifradas.
    - Escalabilidad para soportar cientos de alumnos.

## 3. DISEÑO Y ARQUITECTURA
*¿Cómo está construido por dentro?*

- **Arquitectura:** Patrón **MVC (Modelo-Vista-Controlador)** implementado en PHP nativo.
- **Base de Datos:** Explicar el modelo relacional (puedes referenciar `diagramas.md`).
- **Interfaz de Usuario:** Uso de CSS nativo para un diseño ligero y responsive.

## 4. DESARROLLO TÉCNICO
*Las tripas del código.*

- **Backend:** Uso de PHP 8.2 y PDO para la conexión segura a la DB.
- **APIs de Terceros:**
    - **Firebase (FCM):** Explicar cómo se guardan los tokens y cómo se envían las notificaciones.
    - **Brevo API:** Explicar el flujo de envío de correos electrónicos.
- **Generación de PDFs:** Uso de la librería FPDF para maquetar boletines oficiales.

## 5. PRUEBAS Y VALIDACIÓN
*¿Cómo sabemos que funciona bien?*

- **Pruebas Unitarias:** Validación de formularios (campos vacíos, formatos de email).
- **Pruebas de Integración:** Verificar que al poner una nota, el alumno recibe la notificación correctamente.
- **Feedback Real:** Menciona que los profesores han valorado positivamente la facilidad para calificar retos.

---

### 💡 Preguntas Frecuentes para tu Defensa (FAQs)

- **¿Por qué no usaste un Framework como Laravel?**
  *Respuesta:* "Para demostrar un conocimiento profundo de la arquitectura MVC y la lógica subyacente de PHP, evitando la 'magia' de los frameworks y manteniendo el sistema ligero y fácil de desplegar en cualquier servidor XAMPP estándar."

- **¿Cómo calculas la nota final?**
  *Respuesta:* "El sistema realiza un cálculo dinámico: obtiene el promedio de las notas de los módulos (75%) y lo suma al promedio de los retos asociados (25%), permitiendo una evaluación continua real."

- **¿Es seguro el sistema?**
  *Respuesta:* "Se han implementado validaciones en todos los controladores, sesiones seguras por rol y se ha preparado la estructura para el uso de sentencias preparadas contra inyección SQL."

- **¿Qué es lo más difícil que has hecho?**
  *Respuesta:* "La integración de las notificaciones push de Firebase y la lógica de asignación dinámica de retos a múltiples módulos."