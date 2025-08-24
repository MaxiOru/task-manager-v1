<?php
// Importa el modelo de tareas
require_once '../models/Task.php';

/**
 * Clase TaskController
 * Encapsula la lógica de negocio relacionada con las tareas.
 */
class TaskController {
    private $taskModel;

    public function __construct() {
        // Instancia el modelo de tareas
        $this->taskModel = new Task();
    }

    /**
     * Obtiene todas las tareas asociadas a un usuario.
     */
    public function getTasksByUser($user_id) {
        return $this->taskModel->getByUser($user_id);
    }

    /**
     * Crea una nueva tarea para el usuario especificado.
     */
    public function createTask($user_id, $title, $description, $due_date) {
        return $this->taskModel->create($user_id, $title, $description, $due_date);
    }

    /**
     * Actualiza el estado de una tarea si pertenece al usuario.
     */
    public function updateStatus($task_id, $status, $user_id) {
        // Verifica que la tarea exista y pertenezca al usuario
        $task = $this->taskModel->getById($task_id);
        if ($task && $task['user_id'] == $user_id) {
            return $this->taskModel->updateStatus($task_id, $status);
        }
        // No se actualiza si la tarea no pertenece al usuario
        return false;
    }
}
?>
