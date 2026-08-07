<?php
declare(strict_types=1);

// GET  /api/v1/planificacion.php?action=list    — shared director+secretaria planning checklist
// POST /api/v1/planificacion.php?action=create  — body: {texto}
// POST /api/v1/planificacion.php?action=toggle  — body: {id, completada}
// POST /api/v1/planificacion.php?action=delete  — body: {id}

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/planificacion.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('No tienes permisos para acceder a la planificación.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET' && $action === 'list') {
    v1Ok(['tareas' => listarPlanificacion()]);
}

if ($method === 'POST') {
    $body = v1Body();

    if ($action === 'create') {
        $texto = trim((string)($body['texto'] ?? ''));
        if ($texto === '') v1Error('texto is required.', 400, 'validation');
        if (mb_strlen($texto) > 500) v1Error('texto must be 500 characters or fewer.', 400, 'validation');

        $id = insertarPlanTarea($texto, $type, $uid);
        if ($id === false) v1Error('Could not save the item.', 500, 'error');
        v1Ok(['idPlanTarea' => $id], 201);
    }

    if ($action === 'toggle') {
        $id = (int)($body['id'] ?? 0);
        if ($id <= 0) v1Error('id is required.', 400, 'validation');
        $completada = (bool)($body['completada'] ?? false);

        // Same "who completed it" attribution the web version records — without
        // this, a task checked off from the app showed up in the web history
        // with no name, only the ones completed from the browser did.
        $nombreCompletadaPor = null;
        if ($completada) {
            if ($type === 'director') {
                require_once __DIR__ . '/../../modelos/directores.php';
                $nombreCompletadaPor = obtenerDirectorPorId($uid)['nombreDirector'] ?? 'Director/a';
            } else {
                require_once __DIR__ . '/../../modelos/secretarias.php';
                $nombreCompletadaPor = obtenerSecretariaPorId($uid)['nombreSecretaria'] ?? 'Secretaría';
            }
        }

        if (!togglePlanTarea($id, $completada, $completada ? $type : null, $nombreCompletadaPor)) {
            v1Error('Could not update the item.', 500, 'error');
        }
        v1Ok(['message' => 'Updated.', 'completadaPorNombre' => $nombreCompletadaPor]);
    }

    if ($action === 'delete') {
        $id = (int)($body['id'] ?? 0);
        if ($id <= 0) v1Error('id is required.', 400, 'validation');
        if (!borrarPlanTarea($id)) v1Error('Could not delete the item.', 500, 'error');
        v1Ok(['message' => 'Deleted.']);
    }

    v1Error('Acción no válida.', 400, 'validation');
}

v1Error('Ruta no válida.', 400, 'validation');
