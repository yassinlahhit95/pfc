<?php
/**
 * Tests para la clase Security
 */

use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase {

    public function setUp(): void {
        // Inicializar sesión para cada test
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        Security::initSession();
    }

    /**
     * Test: Generar token CSRF
     */
    public function testGenerateCSRFToken() {
        $token = Security::generateCSRFToken();
        $this->assertNotEmpty($token);
        $this->assertEquals(strlen($token), 64); // bin2hex(32 bytes) = 64 caracteres
    }

    /**
     * Test: Token CSRF generado es consistente
     */
    public function testCSRFTokenConsistency() {
        $token1 = Security::generateCSRFToken();
        $token2 = Security::generateCSRFToken();
        $this->assertEquals($token1, $token2);
    }

    /**
     * Test: Validar token CSRF correcto
     */
    public function testValidateCorrectCSRFToken() {
        $token = Security::generateCSRFToken();
        $this->assertTrue(Security::validateCSRFToken($token));
    }

    /**
     * Test: Rechazar token CSRF incorrecto
     */
    public function testRejectIncorrectCSRFToken() {
        Security::generateCSRFToken();
        $this->assertFalse(Security::validateCSRFToken('incorrect_token'));
    }

    /**
     * Test: Validar email válido
     */
    public function testValidateValidEmail() {
        $this->assertTrue(Security::validateEmail('usuario@example.com'));
        $this->assertTrue(Security::validateEmail('test.user@domain.co.uk'));
    }

    /**
     * Test: Rechazar email inválido
     */
    public function testRejectInvalidEmail() {
        $this->assertFalse(Security::validateEmail('invalid.email'));
        $this->assertFalse(Security::validateEmail('user@'));
        $this->assertFalse(Security::validateEmail('@example.com'));
    }

    /**
     * Test: Validar contraseña fuerte
     */
    public function testValidateStrongPassword() {
        $result = Security::validatePassword('SecurePass123');
        $this->assertTrue($result['valid']);
    }

    /**
     * Test: Rechazar contraseña débil (sin mayúscula)
     */
    public function testRejectPasswordWithoutUppercase() {
        $result = Security::validatePassword('securepass123');
        $this->assertFalse($result['valid']);
    }

    /**
     * Test: Rechazar contraseña débil (sin minúscula)
     */
    public function testRejectPasswordWithoutLowercase() {
        $result = Security::validatePassword('SECUREPASS123');
        $this->assertFalse($result['valid']);
    }

    /**
     * Test: Rechazar contraseña débil (sin número)
     */
    public function testRejectPasswordWithoutNumber() {
        $result = Security::validatePassword('SecurePass');
        $this->assertFalse($result['valid']);
    }

    /**
     * Test: Rechazar contraseña demasiado corta
     */
    public function testRejectShortPassword() {
        $result = Security::validatePassword('Pass1');
        $this->assertFalse($result['valid']);
    }

    /**
     * Test: Hash de contraseña
     */
    public function testHashPassword() {
        $password = 'SecurePass123';
        $hash = Security::hashPassword($password);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(Security::verifyPassword($password, $hash));
    }

    /**
     * Test: Verificar contraseña incorrecta
     */
    public function testVerifyIncorrectPassword() {
        $hash = Security::hashPassword('SecurePass123');
        $this->assertFalse(Security::verifyPassword('WrongPassword', $hash));
    }

    /**
     * Test: Sanitizar entrada
     */
    public function testSanitizeInput() {
        $input = '<script>alert("xss")</script>texto';
        $sanitized = Security::sanitize($input);
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringContainsString('texto', $sanitized);
    }

    /**
     * Test: Validar DNI español válido
     */
    public function testValidateDNIValid() {
        $this->assertTrue(Security::validateDNI('12345678Z'));
    }

    /**
     * Test: Rechazar DNI inválido
     */
    public function testValidateDNIInvalid() {
        $this->assertFalse(Security::validateDNI('12345678A')); // Letra incorrecta
        $this->assertFalse(Security::validateDNI('invalid'));
    }

    /**
     * Test: Validar URL válida
     */
    public function testValidateURL() {
        $this->assertTrue(Security::validateURL('https://example.com'));
        $this->assertTrue(Security::validateURL('http://subdomain.example.co.uk'));
    }

    /**
     * Test: Rechazar URL inválida
     */
    public function testRejectInvalidURL() {
        $this->assertFalse(Security::validateURL('not a url'));
        $this->assertFalse(Security::validateURL('example.com')); // Sin protocolo
    }

    /**
     * Test: Rate limiting - Permitir en primer intento
     */
    public function testRateLimitFirstAttempt() {
        $result = Security::checkRateLimit('user@example.com');
        $this->assertTrue($result['allowed']);
    }

    /**
     * Test: Rate limiting - Bloquear después de 5 intentos
     */
    public function testRateLimitBlocking() {
        $email = 'user@example.com';

        // Registrar 5 intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            Security::recordFailedLogin($email);
        }

        $result = Security::checkRateLimit($email);
        $this->assertFalse($result['allowed']);
        $this->assertArrayHasKey('remaining_time', $result);
    }

    /**
     * Test: Limpiar intentos después de login exitoso
     */
    public function testClearFailedLogins() {
        $email = 'user@example.com';
        Security::recordFailedLogin($email);

        $result = Security::checkRateLimit($email);
        $this->assertEquals($result['attempts'], 1);

        Security::clearFailedLogins($email);

        $result = Security::checkRateLimit($email);
        $this->assertEquals($result['attempts'], 0);
    }
}
?>
