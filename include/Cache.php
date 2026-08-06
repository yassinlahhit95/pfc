<?php
// Caché compartida de tipo read-through para lecturas costosas y repetidas
// con frecuencia (contadores del dashboard, mensajes no leídos, etc).
// Usa APCu cuando está disponible (compartida entre workers/procesos, persiste
// entre peticiones). Sin APCu, cae a un array estático por petición — sigue
// evitando trabajo duplicado dentro de la misma petición, pero no persiste
// entre peticiones HTTP.
class Cache
{
    // APCu es un único espacio de memoria compartida por pool de PHP-FPM. Los IDs
    // de fila (idEstudiante, idProfesor, ...) solo son únicos *dentro* de la base
    // de datos de un tenant, así que si dos tenants llegan a compartir pool, una
    // clave sin prefijo como "unread_5" colisionaría entre el usuario #5 del
    // tenant A y el usuario #5 del tenant B. Prefijar aquí de forma transparente
    // hace que cada llamador tenga aislamiento gratis, sin tener que acordarse.
    private static function tenantKey(string $key): string
    {
        require_once __DIR__ . '/../config/Config.php';
        return Config::getInstance()->get('R2_TENANT_PREFIX', 'default') . ':' . $key;
    }

    public static function remember(string $key, int $ttlSeconds, callable $compute)
    {
        $key = self::tenantKey($key);
        if (function_exists('apcu_fetch')) {
            $val = apcu_fetch($key, $found);
            if ($found) return $val;
            $val = $compute();
            apcu_store($key, $val, $ttlSeconds);
            return $val;
        }
        static $mem = [];
        $now = time();
        if (isset($mem[$key]) && $mem[$key]['expires'] > $now) {
            return $mem[$key]['value'];
        }
        $val = $compute();
        $mem[$key] = ['value' => $val, 'expires' => $now + $ttlSeconds];
        return $val;
    }

    public static function forget(string $key): void
    {
        if (function_exists('apcu_delete')) {
            apcu_delete(self::tenantKey($key));
        }
    }
}

// Limitador barato, sin BD, para endpoints de sondeo (polling) muy solicitados.
// allow() devuelve true como máximo una vez cada $minIntervalSeconds para una
// clave dada. Es una red de seguridad contra un cliente atascado o duplicado
// que machaque un endpoint más rápido de lo que el sondeo del cliente haría
// nunca — no es un rate limit de cara al usuario, así que falla abierto
// (permite) cuando APCu no está disponible, en vez de bloquear tráfico.
class Throttle
{
    public static function allow(string $key, float $minIntervalSeconds): bool
    {
        if (!function_exists('apcu_fetch')) return true;
        $now = microtime(true);
        $last = apcu_fetch($key, $found);
        if ($found && ($now - $last) < $minIntervalSeconds) return false;
        apcu_store($key, $now, (int)ceil($minIntervalSeconds) + 1);
        return true;
    }
}
