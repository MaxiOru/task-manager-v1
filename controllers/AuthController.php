<?php
session_start();
require_once '../config/database.php';

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
            $_SESSION['user_role'] = $user['role']; // 👈 importante para saber el rol

            // Redirigir según el rol
            if ($user['role'] === 'jefe') {
                header("Location: views/dashboard_jefe.php");
            } else {
                header("Location: views/dashboard_usuario.php");
            }
            exit;
        } else {
            return "Usuario o contraseña incorrectos.";
        }
    }

    public function logout() {
        session_destroy();
        header("Location: views/login.php");
        exit;
    }

}
?>