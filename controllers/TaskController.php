<?php
require_once '../models/Task.php';

class TaskController {
    private $taskModel;

    public function __construct() {
        $this->taskModel = new Task();
    }

    public function getTasksByUser($user_id) {
        return $this->taskModel->getByUser($user_id);
    }

    public function createTask($user_id, $title, $description, $due_date) {
        return $this->taskModel->create($user_id, $title, $description, $due_date);
    }

    public function updateStatus($task_id, $status, $user_id) {
        // Validar que la tarea pertenece al usuario
        $task = $this->taskModel->getById($task_id);
        if ($task && $task['user_id'] == $user_id) {
            return $this->taskModel->updateStatus($task_id, $status);
        }
        return false;
    }
}
?>