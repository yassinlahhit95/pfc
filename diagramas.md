# Diagramas de AulaPro

Aquí están los esquemas de cómo está montada la base de datos y cómo funciona la parte de las notas.

## Diagrama Entidad-Relación (Base de Datos)

```mermaid
erDiagram
    NIVELES {
        int idNivel PK
        string nombreNivel
    }
    CICLOS {
        int idCiclo PK
        string nombreCiclo
        string abreviaturaCiclo
        decimal precioCiclo
        int idNivel FK
    }
    MODULOS {
        int idModulo PK
        string nombreModulo
        int horasMaximas
        int idCiclo FK
    }
    PROFESORES {
        int idProfesor PK
        string nombreProfesor
        string emailProfesor
        string telefonoProfesor
        string dniProfesor
        string fcm_token
    }
    ESTUDIANTES {
        int idEstudiante PK
        string nombreEstudiante
        string emailEstudiante
        int idCiclo FK
        string archivoTFG
        string fcm_token
    }
    DIRECTORES {
        int idDirector PK
        string nombreDirector
        string emailDirector
        string fcm_token
    }
    RETOS {
        int idReto PK
        string nombreReto
        date fechaInicio
        date fechaFin
        int horasReto
    }
    MODULO_RETO {
        int idModulo FK
        int idReto FK
    }
    CALIFICACIONES_RETOS {
        int idCalificacion PK
        int idEstudiante FK
        int idReto FK
        decimal nota
    }
    CALIFICACIONES_TFG {
        int idCalificacion PK
        int idEstudiante FK
        decimal nota
        text observaciones
    }
    CALIFICACIONES_MODULOS {
        int idCalificacion PK
        int idEstudiante FK
        int idModulo FK
        decimal nota_1ev
        decimal nota_1final
        decimal nota_2ev
        decimal nota_2final
    }
    DISPOSITIVOS {
        int idDispositivo PK
        string nombreDispositivo
        string numeroSerie
        enum estadoDispositivo
    }
    PRESTAMOS {
        int idPrestamo PK
        int idEstudiante FK
        string numeroSerie
        date fechaPrestamo
        enum estadoPrestamo
    }
    ANUNCIOS {
        int idAnuncio PK
        string titulo
        text mensaje
        date fechaExpiracion
        enum dirigidoA
    }
    RECLAMACIONES {
        int idReclamacion PK
        int idEstudiante FK
        int idProfesor FK
        enum emisor_rol
        string asunto
        text descripcion
        enum estadoReclamacion
    }
    PAGOS {
        int idPago PK
        int idEstudiante FK
        decimal monto
        date fechaPago
        date fechaProximoPago
        enum tipoPago
    }
    EVENTOS {
        int idEvento PK
        string tituloEvento
        text descripcionEvento
        date fechaEvento
        time horaEvento
        string ubicacionEvento
    }
    CICLO_PROFESOR {
        int idCiclo FK
        int idProfesor FK
    }
    PROFESOR_MODULO {
        int idProfesor FK
        int idModulo FK
    }

    NIVELES ||--o{ CICLOS : "contiene"
    CICLOS ||--o{ MODULOS : "incluye"
    CICLOS ||--o{ ESTUDIANTES : "ofrece"
    CICLOS ||--o{ CICLO_PROFESOR : "asocia"
    MODULOS ||--o{ MODULO_RETO : "relaciona"
    MODULOS ||--o{ PROFESOR_MODULO : "asigna"
    RETOS ||--o{ MODULO_RETO : "agrega"
    PROFESORES ||--o{ PROFESOR_MODULO : "imparte"
    PROFESORES ||--o{ CICLO_PROFESOR : "participa"
    ESTUDIANTES ||--o{ CALIFICACIONES_RETOS : "recibe"
    ESTUDIANTES ||--o{ CALIFICACIONES_MODULOS : "recibe"
    ESTUDIANTES ||--o{ PRESTAMOS : "toma"
    ESTUDIANTES ||--o{ PAGOS : "paga"
    ESTUDIANTES ||--o{ RECLAMACIONES : "envía"
    PROFESORES ||--o{ RECLAMACIONES : "recibe"
    ESTUDIANTES ||--o{ CALIFICACIONES_TFG : "recibe nota"
```

## Flujo de Calificación (Secuencia)

```mermaid
sequenceDiagram
    participant P as Profesor
    participant S as Sistema
    participant M as Modelo
    participant B as BD
    participant E as Estudiante

    P->>S: Selecciona Módulo
    S->>M: buscarRetos($idModulo)
    M->>B: SELECT...
    B-->>M: Retos
    M-->>S: Lista
    S-->>P: Muestra retos

    P->>S: Pone la nota
    S->>M: guardarNota($idEstu, $idReto, $nota)
    M->>B: INSERT/UPDATE...
    B-->>M: OK
    M-->>S: OK
    S-->>P: Guardado

    Note over S,E: Notificación al alumno
    E->>S: Mira sus notas
    S->>M: verNotas($idEstu)
    M->>B: SELECT...
    B-->>M: Datos
    M-->>S: Calificaciones
    S-->>E: Ver notas
```

## Listado de Tablas
- `niveles`: Grado Medio o Superior.
- `ciclos`: DAW, DAM, SMR, etc.
- `modulos`: Asignaturas.
- `profesores`: Datos del equipo docente.
- `estudiantes`: Alumnos matriculados.
- `directores`: Administradores.
- `retos`: Proyectos ABP.
- `modulo_reto`: Qué retos van con qué módulos.
- `calificaciones_retos`: Notas de los proyectos.
- `calificaciones_modulos`: Notas de exámenes.
- `dispositivos` y `prestamos`: Gestión de portátiles.
- `anuncios`: Tablón de avisos.
- `eventos`: Calendario escolar.
- `reclamaciones`: Mensajes internos.
- `pagos`: Control de cuotas.
- `ciclo_profesor` y `profesor_modulo`: Asignaciones de trabajo.
