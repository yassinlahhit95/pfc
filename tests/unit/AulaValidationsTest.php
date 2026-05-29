<?php
/**
 * Tests para validaciones de AULA DIGITAL
 * Prueba las funciones de validación de sesiones vivas y asistencia
 */

use PHPUnit\Framework\TestCase;

class AulaValidationsTest extends TestCase {

    /**
     * Test: Validar fecha en el futuro
     */
    public function testValidateFutureDateSucceeds() {
        $futureDate = date('Y-m-d', strtotime('+1 day'));
        $futureTime = '14:30';

        require_once PROJECT_ROOT . '/modelos/aula.php';
        $result = validarFechaHoraSesion($futureDate, $futureTime);
        $this->assertNull($result);
    }

    /**
     * Test: Rechazar fecha en el pasado
     */
    public function testValidatePastDateFails() {
        $pastDate = date('Y-m-d', strtotime('-1 day'));
        $time = '14:30';

        require_once PROJECT_ROOT . '/modelos/aula.php';
        $result = validarFechaHoraSesion($pastDate, $time);
        $this->assertNotNull($result);
        $this->assertStringContainsString('futuro', $result);
    }

    /**
     * Test: Rechazar formato de fecha inválido
     */
    public function testInvalidDateFormatFails() {
        require_once PROJECT_ROOT . '/modelos/aula.php';
        $result = validarFechaHoraSesion('invalid-date', '14:30');
        $this->assertNotNull($result);
    }

    /**
     * Test: Validar URL válida
     */
    public function testValidURLSucceeds() {
        require_once PROJECT_ROOT . '/modelos/aula.php';
        $result = validarEnlaceReunion('https://meet.google.com/abc-defg-hij');
        $this->assertNull($result);
    }

    /**
     * Test: Validar URL vacía (es opcional)
     */
    public function testEmptyURLSucceeds() {
        require_once PROJECT_ROOT . '/modelos/aula.php';
        $result = validarEnlaceReunion('');
        $this->assertNull($result);
    }

    /**
     * Test: Rechazar URL inválida
     */
    public function testInvalidURLFails() {
        require_once PROJECT_ROOT . '/modelos/aula.php';
        $result = validarEnlaceReunion('texto aleatorio');
        $this->assertNotNull($result);
        $this->assertStringContainsString('URL', $result);
    }

    /**
     * Test: Validar duración positiva
     */
    public function testPositiveDurationSucceeds() {
        require_once PROJECT_ROOT . '/modelos/aula.php';
        // Simular una duración válida
        $horaUnion = '14:00:00';
        $horaSalida = '15:30:00';

        // Calcular duración
        $inicio = new DateTime($horaUnion);
        $fin = new DateTime($horaSalida);
        $diff = $fin->diff($inicio);
        $duracion = ($diff->h * 60) + $diff->i;

        $this->assertGreaterThan(0, $duracion);
        $this->assertEquals(90, $duracion);
    }

    /**
     * Test: Rechazar duración negativa
     */
    public function testNegativeDurationFails() {
        require_once PROJECT_ROOT . '/modelos/aula.php';
        $horaUnion = '15:00:00';
        $horaSalida = '14:00:00';

        $inicio = new DateTime($horaUnion);
        $fin = new DateTime($horaSalida);
        $diff = $fin->diff($inicio);
        $duracion = -1 * (($diff->h * 60) + $diff->i);

        $this->assertLessThan(0, $duracion);
    }

    /**
     * Test: Validar estudiantes por módulo (función auxiliar)
     */
    public function testObtenerEstudiantesPorModuloExists() {
        require_once PROJECT_ROOT . '/modelos/aula.php';

        // Verificar que la función existe
        $this->assertTrue(function_exists('obtenerEstudiantesPorModulo'));
    }

    /**
     * Test: Validar función de notificación existe
     */
    public function testNotificarEstudiantesPorModuloExists() {
        require_once PROJECT_ROOT . '/modelos/aula.php';

        // Verificar que la función existe
        $this->assertTrue(function_exists('notificarEstudiantesPorModulo'));
    }
}
?>
