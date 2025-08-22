<?php
require_once '../config/database.php';

class User {
    private $conn;
    private $table = "users";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Obtener usuario por username
    public function getByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo usuario
    public function create($username, $password, $role = 'normal') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (username, password, role, approved) VALUES (:username, :password, :role, 0)");
        return $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword,
            'role' => $role
        ]);
    }

    // Verificar si usuario existe
    public function exists($username) {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() !== false;
    }

    // Aprobar usuario
    public function approve($user_id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET approved = 1 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    // Obtener usuarios pendientes de aprobación
    public function getPendingUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE role = 'normal' AND approved = 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuarios aprobados
    public function getApprovedUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE role = 'normal' AND approved = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}