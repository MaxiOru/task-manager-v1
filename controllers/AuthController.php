<?php
// Inicia la sesión si aún no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye la configuración de la base de datos
require_once __DIR__ . '/../config/database.php';

/**
 * Clase AuthController
 * Maneja la autenticación de usuarios: login y logout.
 */
class AuthController {
    private $conn;

    public function __construct() {
        // Establece la conexión a la base de datos
        $db = new Database();
        $this->conn = $db->connect();
    }

    /**
     * Autentica al usuario con nombre de usuario y contraseña.
     * Si es válido, guarda datos en sesión y redirige según el rol.
     */
    public function login($username, $password) {
        // Consulta el usuario por nombre
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica la contraseña y si el usuario está aprobado
        if ($user && password_verify($password, $user['password'])) {

            if ($user['approved'] == 0) {
                return "Tu cuenta aún no ha sido aprobada por el jefe.";
            }

            // Guarda datos relevantes en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            // Redirige al dashboard correspondiente según el rol
            if ($user['role'] === 'jefe') {
                header("Location: /task-manager-v1/views/dashboard_jefe.php");
            } else {
                header("Location: /task-manager-v1/views/dashboard_usuario.php");
            }
            exit;
        } else {
            // Credenciales inválidas
            return "Usuario o contraseña incorrectos.";
        }
    }

    /**
     * Cierra la sesión del usuario y redirige al inicio.
     */
    public function logout() {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
}
?>
