<?php
// Importa la configuración de la base de datos
require_once '../config/database.php';

/**
 * Clase Task
 * Encapsula operaciones CRUD relacionadas con tareas.
 */
class Task {
    private $conn;
    private $table = "tasks";

    public function __construct() {
        // Establece la conexión a la base de datos
        $db = new Database();
        $this->conn = $db->connect();
    }

    /**
     * Obtiene todas las tareas asociadas a un usuario, ordenadas por fecha de vencimiento.
     */
    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY due_date ASC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva tarea con estado inicial 'pendiente'.
     */
    public function create($user_id, $title, $description, $due_date) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (user_id, title, description, due_date, status) 
            VALUES (:user_id, :title, :description, :due_date, 'pendiente')"
        );
        return $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'due_date' => $due_date
        ]);
    }

    /**
     * Actualiza el estado de una tarea si el nuevo estado es válido.
     */
    public function updateStatus($task_id, $status) {
        $validStatuses = ['pendiente', 'en progreso', 'completada'];
        
        // Verifica que el estado sea uno de los permitidos
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'id' => $task_id
        ]);
    }

    /**
     * Obtiene una tarea por su ID (útil para validaciones previas).
     */
    public function getById($task_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
