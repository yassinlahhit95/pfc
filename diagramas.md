# Diagramas de AulaPro

Este documento contiene la representación visual de la arquitectura de datos y los flujos de trabajo principales del sistema AulaPro.

## 1. Diagrama Entidad-Relación (Base de Datos)

El sistema utiliza una base de datos relacional MySQL con 20 tablas organizadas para soportar la gestión multi-rol.

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
        int curso
    }
    PROFESORES {
        int idProfesor PK
        string nombreProfesor
        string emailProfesor
        string password
        string dniProfesor
        string telefonoProfesor
        string direccionProfesor
        string fcm_token
    }
    ESTUDIANTES {
        int idEstudiante PK
        string nombreEstudiante
        string emailEstudiante
        string password
        string dniEstudiante
        int idCiclo FK
        string archivoTFG
        string tituloTFG
        datetime fechaSubidaTFG
        string fcm_token
    }
    DIRECTORES {
        int idDirector PK
        string nombreDirector
        string emailDirector
        string password
        string dniDirector
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
        text observaciones
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
        date fechaDevolucion
        enum estadoPrestamo
    }
    ANUNCIOS {
        int idAnuncio PK
        string titulo
        text mensaje
        datetime fechaAnuncio
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
        date fecha
        enum estadoReclamacion
        boolean leido
        text respuesta
    }
    PAGOS {
        int idPago PK
        int idEstudiante FK
        decimal monto
        date fechaPago
        date fechaProximoPago
        enum tipoPago
        string comprobante
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

    NIVELES ||--o{ CICLOS : "agrupa"
    CICLOS ||--o{ MODULOS : "contiene"
    CICLOS ||--o{ ESTUDIANTES : "matricula a"
    CICLOS ||--o{ CICLO_PROFESOR : "asocia"
    MODULOS ||--o{ MODULO_RETO : "participa en"
    MODULOS ||--o{ PROFESOR_MODULO : "impartido por"
    RETOS ||--o{ MODULO_RETO : "abarca"
    PROFESORES ||--o{ PROFESOR_MODULO : "imparte"
    PROFESORES ||--o{ CICLO_PROFESOR : "participa en"
    ESTUDIANTES ||--o{ CALIFICACIONES_RETOS : "recibe"
    ESTUDIANTES ||--o{ CALIFICACIONES_MODULOS : "recibe"
    ESTUDIANTES ||--o{ CALIFICACIONES_TFG : "recibe nota"
    ESTUDIANTES ||--o{ PRESTAMOS : "solicita"
    DISPOSITIVOS ||--o{ PRESTAMOS : "es prestado"
    ESTUDIANTES ||--o{ PAGOS : "realiza"
    ESTUDIANTES ||--o{ RECLAMACIONES : "envía/recibe"
    PROFESORES ||--o{ RECLAMACIONES : "recibe/envía"
```

## 2. Flujo de Calificación y Notificación

```mermaid
sequenceDiagram
    participant P as Profesor
    participant S as Servidor (PHP)
    participant B as Base de Datos
    participant F as Firebase/Brevo
    participant E as Estudiante

    P->>S: Introduce nota de Módulo/Reto
    S->>B: INSERT/UPDATE Calificación
    B-->>S: Confirmación
    
    rect rgb(240, 240, 240)
    Note over S,F: Proceso de Notificación
    S->>F: Enviar Push (FCM) / Email (Brevo)
    F-->>E: Notificación recibida
    end

    E->>S: Accede a Portal Estudiante
    S->>B: SELECT Calificaciones
    B-->>S: Datos del estudiante
    S-->>E: Muestra boletín actualizado
```

## 3. Descripción de Bloques Funcionales
- **Estructura Académica:** Control de Niveles, Ciclos y Módulos.
- **Gestión de Usuarios:** Repositorio centralizado de Directores, Profesores y Estudiantes con autenticación propia.
- **Evaluación Mixta:** Sistema que combina calificaciones tradicionales de módulos con la metodología ABP (Retos).
- **Servicios del Centro:** Gestión de inventario tecnológico, control de cobros (Pagos) y organización de eventos.
- **Comunicación Interna:** Tablón de anuncios, mensajería de reclamaciones y sistema de notificaciones push.
