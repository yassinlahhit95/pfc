<?php
/**
 * Shared error/success messages across AulaPro.
 * Single source of truth for user-facing strings to ensure consistency.
 * Use: ErrorMessages::INVALID_REQUEST, ErrorMessages::NO_PERMISSION, etc.
 */
class ErrorMessages {
    // HTTP/Request errors
    const INVALID_REQUEST = 'Solicitud inválida. Inténtelo de nuevo.';
    const MISSING_PARAMS = 'Parámetros faltantes.';
    const INVALID_FORMAT = 'Formato inválido.';

    // Permission/Auth errors
    const NO_PERMISSION = 'No tienes permiso para realizar esta acción.';
    const UNAUTHORIZED = 'Debes estar autenticado para continuar.';
    const SESSION_EXPIRED = 'Tu sesión ha expirado. Recarga la página.';

    // Validation errors
    const INVALID_EMAIL = 'El formato del correo electrónico no es válido.';
    const INVALID_PASSWORD = 'La contraseña no cumple los requisitos.';
    const PASSWORD_MISMATCH = 'Las contraseñas no coinciden.';

    // Not found errors
    const NOT_FOUND = 'El registro solicitado no existe o fue eliminado.';
    const USER_NOT_FOUND = 'El usuario no existe.';

    // Conflict/duplicate errors
    const DUPLICATE_ENTRY = 'Este registro ya existe.';
    const EMAIL_IN_USE = 'Este correo ya está registrado.';

    // File errors
    const FILE_UPLOAD_FAILED = 'Error al subir el archivo. Verifica que el tipo y tamaño sean válidos.';
    const FILE_TOO_LARGE = 'El archivo excede el tamaño máximo permitido.';
    const INVALID_FILE_TYPE = 'El tipo de archivo no está permitido.';

    // Database errors
    const DATABASE_ERROR = 'Error en la base de datos. Contacta con soporte si persiste.';
    const SAVE_FAILED = 'No se pudo guardar el registro. Verifica los datos e inténtelo de nuevo.';
    const DELETE_FAILED = 'No se pudo eliminar el registro. Puede estar en uso.';

    // Success messages
    const SAVE_SUCCESS = 'Guardado correctamente.';
    const DELETE_SUCCESS = 'Eliminado correctamente.';
    const UPDATE_SUCCESS = 'Actualizado correctamente.';
    const ACTION_SUCCESS = 'Acción completada correctamente.';
    const SEND_SUCCESS = 'Enviado correctamente.';
}
