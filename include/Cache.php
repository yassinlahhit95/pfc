<?php
// Shared read-through cache for expensive, frequently-repeated reads
// (dashboard counters, unread-message counts, etc).
// Uses APCu when available (shared across workers/processes, survives
// requests). Without APCu, falls back to a per-request static array —
// still avoids duplicate work within the same request, but does not
// persist between HTTP requests.
class Cache
{
    public static function remember(string $key, int $ttlSeconds, callable $compute)
    {
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
            apcu_delete($key);
        }
    }
}

// Cheap, no-DB throttle for hot polling endpoints. allow() returns true at
// most once per $minIntervalSeconds for a given key. This is a safety net
// against a stuck/duplicated client hammering an endpoint faster than the
// client-side poller ever would — not a user-facing rate limit, so it
// fails open (allows) when APCu is unavailable rather than blocking traffic.
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
