<?php
/**
 * Tests para la clase Logger
 */

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {

    private $testLogDir;

    public function setUp(): void {
        $this->testLogDir = sys_get_temp_dir() . '/aulapro_tests_' . uniqid();
        mkdir($this->testLogDir, 0755, true);
    }

    public function tearDown(): void {
        // Limpiar archivos de test
        array_map('unlink', glob("$this->testLogDir/*.log"));
        rmdir($this->testLogDir);
    }

    /**
     * Test: Logger se inicializa correctamente
     */
    public function testLoggerInitialize() {
        Logger::init($this->testLogDir);
        $this->assertTrue(is_dir($this->testLogDir));
    }

    /**
     * Test: Escribir error log
     */
    public function testErrorLogging() {
        Logger::init($this->testLogDir);
        Logger::error('Test error', ['code' => 500]);

        $errorLog = $this->testLogDir . '/error.log';
        $this->assertTrue(file_exists($errorLog));
        $content = file_get_contents($errorLog);
        $this->assertStringContainsString('ERROR', $content);
        $this->assertStringContainsString('Test error', $content);
    }

    /**
     * Test: Escribir log de actividad
     */
    public function testActivityLogging() {
        Logger::init($this->testLogDir);
        Logger::activity('LOGIN_SUCCESS', 123, ['role' => 'admin']);

        $activityLog = $this->testLogDir . '/activity.log';
        $this->assertTrue(file_exists($activityLog));
        $content = file_get_contents($activityLog);
        $this->assertStringContainsString('ACTIVITY', $content);
        $this->assertStringContainsString('LOGIN_SUCCESS', $content);
    }

    /**
     * Test: Escribir log de seguridad
     */
    public function testSecurityLogging() {
        Logger::init($this->testLogDir);
        Logger::security('FAILED_LOGIN', ['email' => 'user@example.com', 'ip' => '127.0.0.1']);

        $securityLog = $this->testLogDir . '/security.log';
        $this->assertTrue(file_exists($securityLog));
        $content = file_get_contents($securityLog);
        $this->assertStringContainsString('WARNING', $content);
        $this->assertStringContainsString('FAILED_LOGIN', $content);
    }

    /**
     * Test: Escribir log de acceso
     */
    public function testAccessLogging() {
        Logger::init($this->testLogDir);
        Logger::access('/vistas/admin/dashboard.php', 'GET', 200, 123);

        $accessLog = $this->testLogDir . '/access.log';
        $this->assertTrue(file_exists($accessLog));
        $content = file_get_contents($accessLog);
        $this->assertStringContainsString('GET', $content);
        $this->assertStringContainsString('/vistas/admin/dashboard.php', $content);
        $this->assertStringContainsString('[200]', $content);
    }

    /**
     * Test: Logs contienen timestamp
     */
    public function testLogsContainTimestamp() {
        Logger::init($this->testLogDir);
        Logger::info('Test message');

        $infoLog = $this->testLogDir . '/info.log';
        $content = file_get_contents($infoLog);

        // Verificar que contiene formato de timestamp
        $this->assertRegExp('/\[\d{4}-\d{2}-\d{2}/', $content);
    }

    /**
     * Test: Error log también se escribe en critical log
     */
    public function testErrorLogDuplicatedInCritical() {
        Logger::init($this->testLogDir);
        Logger::error('Critical error');

        $errorLog = $this->testLogDir . '/error.log';
        $criticalLog = $this->testLogDir . '/critical.log';

        $this->assertTrue(file_exists($errorLog));
        $this->assertTrue(file_exists($criticalLog));

        $errorContent = file_get_contents($errorLog);
        $criticalContent = file_get_contents($criticalLog);

        $this->assertStringContainsString('Critical error', $errorContent);
        $this->assertStringContainsString('Critical error', $criticalContent);
    }
}
?>
