<?php
// Circuit breaker for external HTTP services (Brevo, FCM).
// States: CLOSED (normal) → OPEN (blocking) → HALF-OPEN (testing) → CLOSED.
// State is stored in APCu when available (shared across workers, survives requests).
// Without APCu, state is per-request static (still protects within-request loops
// like mass-email, but does not persist between HTTP requests).
class CircuitBreaker
{
    private const FAIL_THRESHOLD = 3;   // consecutive failures to open
    private const OPEN_TIMEOUT   = 60;  // seconds before half-open test
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
        return 'cb_' . $service;
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
