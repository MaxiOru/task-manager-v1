<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            if ($user['approved'] == 0) {
                return "Tu cuenta aún no ha sido aprobada por el jefe.";
            }

            // Guardar datos en sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            // Redirigir según el rol
            if ($user['role'] === 'jefe') {
                header("Location: /task-manager-v1/views/dashboard_jefe.php");
            } else {
                header("Location: /task-manager-v1/views/dashboard_usuario.php");
            }
            exit;
        } else {
            return "Usuario o contraseña incorrectos.";
        }
    }

    public function logout() {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
}
?>