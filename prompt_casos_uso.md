# Contexto del Sistema: Portal de Gestión Educativa (PFC)

Este texto sirve como contexto y prompt para generar o comprender el **Diagrama de Casos de Uso** del sistema.

## 1. Descripción General del Proyecto
El sistema es una plataforma integral para un centro de Formación Profesional (FP) que gestiona Ciclos Formativos (ej. Desarrollo de Aplicaciones Web, Sistemas Microinformáticos y Redes). Su objetivo es conectar y coordinar a tres perfiles principales de usuarios: Directores/Administradores, Profesores y Estudiantes. El sistema maneja aspectos académicos (calificaciones basadas en metodologías ágiles y retos), administrativos (pagos, inventario de dispositivos) y comunicativos (eventos, anuncios, reclamaciones).

## 2. Actores del Sistema
Existen 3 actores principales que interactúan con el sistema:
1. **Administrador / Director**: Tiene el máximo privilegio. Gestiona la estructura base del centro.
2. **Profesor**: Encargado de la evaluación académica de los estudiantes en los módulos y retos que tiene asignados.
3. **Estudiante**: Usuario final del sistema que cursa un ciclo, recibe notas y utiliza los servicios del centro.

## 3. Funcionalidades por Actor (Casos de Uso)

### Actor: Administrador / Director
- **Gestión Académica Base**: Crear y modificar Niveles (Grado Medio, Superior), Aulas y Ciclos Formativos.
- **Gestión de Usuarios**: Alta, baja y modificación de Directores, Profesores y Estudiantes.
- **Asignaciones**: Asignar Profesores a Ciclos y Aulas a Ciclos. Asignar Módulos a Profesores.
- **Gestión Curricular**: Crear Módulos (asignaturas) y Retos (proyectos transversales). Vincular Retos con uno o varios Módulos.
- **Gestión Administrativa y Financiera**: Registrar pagos de matrículas/mensualidades de los estudiantes. Gestionar el inventario de Dispositivos (portátiles, proyectores) y registrar Préstamos a estudiantes.
- **Comunicación Global**: Crear Eventos para el calendario escolar y publicar Anuncios globales o dirigidos a grupos específicos.
- **Monitorización**: Ver métricas y estadísticas globales en un Panel de Control (Dashboard).

### Actor: Profesor
- **Evaluación por Módulos**: Introducir y modificar notas (1ª Evaluación, Finales) de los estudiantes matriculados en los módulos que imparte.
- **Evaluación por Retos**: Calificar el desempeño de los estudiantes en los Retos transversales asignados a sus módulos.
- **Gestión de Calificaciones Finales**: Generar promedios (el sistema calcula 75% módulos + 25% retos) y enviar boletines de notas por correo electrónico a los estudiantes.
- **Comunicación y Soporte**: Responder a las Reclamaciones o tutorías enviadas por los estudiantes. Publicar anuncios específicos para sus alumnos.

### Actor: Estudiante
- **Consulta Académica**: Visualizar su expediente, notas de módulos, notas de retos y promedios finales.
- **Trabajo de Fin de Grado (TFG)**: Subir, actualizar o eliminar su archivo de proyecto final (PDF) para ser evaluado.
- **Servicios Administrativos**: Consultar el estado de sus pagos y revisar si tiene dispositivos en préstamo o vencidos.
- **Soporte y Comunicación**: Enviar reclamaciones o mensajes a la administración o a sus profesores. Ver anuncios importantes y eventos del centro.

---

## 4. Código Mermaid para el Diagrama de Casos de Uso

Puedes usar el siguiente bloque para generar el diagrama visual en herramientas compatibles con Mermaid (como draw.io, mermaid.live o plugins de VS Code).

```mermaid
usecaseDiagram
    actor Director as "Administrador / Director"
    actor Profesor
    actor Estudiante

    package "Gestión Académica y Estructural" {
        usecase "Gestionar Ciclos, Módulos y Retos" as UC1
        usecase "Gestionar Usuarios (Alta/Baja)" as UC2
        usecase "Asignar Profesores a Módulos" as UC3
    }

    package "Evaluación Académica" {
        usecase "Calificar Módulos" as UC4
        usecase "Calificar Retos" as UC5
        usecase "Calcular Promedios Finales" as UC6
        usecase "Enviar Notas por Email" as UC7
    }

    package "Portal del Estudiante" {
        usecase "Consultar Notas y Expediente" as UC8
        usecase "Subir/Gestionar TFG" as UC9
    }

    package "Administración y Comunicación" {
        usecase "Gestionar Inventario y Préstamos" as UC10
        usecase "Gestionar Pagos" as UC11
        usecase "Publicar Eventos y Anuncios" as UC12
        usecase "Enviar/Responder Reclamaciones" as UC13
    }

    Director --> UC1
    Director --> UC2
    Director --> UC3
    Director --> UC10
    Director --> UC11
    Director --> UC12
    Director --> UC13

    Profesor --> UC4
    Profesor --> UC5
    Profesor --> UC6
    Profesor --> UC7
    Profesor --> UC12
    Profesor --> UC13

    Estudiante --> UC8
    Estudiante --> UC9
    Estudiante --> UC13
```
*(Nota: En Mermaid estándar se usa diagramas de clase o de grafo para simular casos de uso con mayor fidelidad, pero la estructura conceptual se refleja claramente en el esquema detallado arriba).*
