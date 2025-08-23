<?php
require_once '../config/database.php';

class Task {
    private $conn;
    private $table = "tasks";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Obtener tareas por usuario
    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY due_date ASC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear nueva tarea
    public function create($user_id, $title, $description, $due_date) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (user_id, title, description, due_date, status) VALUES (:user_id, :title, :description, :due_date, 'pendiente')");
        return $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'due_date' => $due_date
        ]);
    }

    // Actualizar estado de tarea
    public function updateStatus($task_id, $status) {
        $validStatuses = ['pendiente', 'en progreso', 'completada'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'id' => $task_id
        ]);
    }

    // Obtener tarea por ID (necesario para validación)
    public function getById($task_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>