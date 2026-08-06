<?php
// Circuit breaker para servicios HTTP externos (Brevo, FCM).
// Estados: CLOSED (normal) → OPEN (bloqueando) → HALF-OPEN (probando) → CLOSED.
// El estado se guarda en APCu cuando está disponible (compartido entre workers, persiste entre peticiones).
// Sin APCu, el estado es estático por petición (sigue protegiendo bucles dentro
// de una misma petición, como el envío masivo de emails, pero no persiste entre peticiones HTTP).
class CircuitBreaker
{
    private const FAIL_THRESHOLD = 3;   // fallos consecutivos para abrir
    private const OPEN_TIMEOUT   = 60;  // segundos antes de la prueba half-open
    private const APCU_TTL       = 300; // 5 min APCu TTL (longer than open timeout)

    public static function isOpen(string $service): bool
    {
        $state = self::getState($service);
        if ($state['status'] !== 'open') return false;

        // Timed out: move to half-open and let one probe through.
        if (time() - ($state['opened_at'] ?? 0) >= self::OPEN_TIMEOUT) {
            self::setState($service, array_merge($state, ['status' => 'half_open']));
            return false;
        }
        return true;
    }

    public static function recordSuccess(string $service): void
    {
        self::setState($service, ['status' => 'closed', 'failures' => 0, 'opened_at' => 0]);
    }

    public static function recordFailure(string $service): void
    {
        $state    = self::getState($service);
        $failures = ((int)($state['failures'] ?? 0)) + 1;

        if ($failures >= self::FAIL_THRESHOLD || $state['status'] === 'half_open') {
            self::setState($service, [
                'status'    => 'open',
                'failures'  => $failures,
                'opened_at' => time(),
            ]);
        } else {
            self::setState($service, array_merge($state, ['failures' => $failures]));
        }
    }

    // ── Storage ──────────────────────────────────────────────────────────

    private static function apcu_key(string $service): string
    {
        require_once __DIR__ . '/../config/Config.php';
        $tenant = Config::getInstance()->get('R2_TENANT_PREFIX', 'default');
        return 'cb_' . $service . ':' . $tenant;
    }

    private static function getState(string $service): array
    {
        if (function_exists('apcu_fetch')) {
            $val = apcu_fetch(self::apcu_key($service), $found);
            if ($found && is_array($val)) return $val;
        }
        static $mem = [];
        return $mem[$service] ?? ['status' => 'closed', 'failures' => 0, 'opened_at' => 0];
    }

    private static function setState(string $service, array $state): void
    {
        if (function_exists('apcu_store')) {
            apcu_store(self::apcu_key($service), $state, self::APCU_TTL);
        }
        static $mem = [];
        $mem[$service] = $state;
    }
}
