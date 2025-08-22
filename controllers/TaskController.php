<?php
require_once '../config/database.php';

class TaskController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getTasksByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY due_date ASC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTask($user_id, $title, $description, $due_date) {
        $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (:user_id, :title, :description, :due_date)");
        $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'due_date' => $due_date
        ]);
    }

    public function updateStatus($task_id, $status) {
        $stmt = $this->conn->prepare("UPDATE tasks SET status = :status WHERE id = :id");
        $stmt->execute([
            'status' => $status,
            'id' => $task_id
        ]);
    }
}