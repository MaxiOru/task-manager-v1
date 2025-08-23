<?php
session_start();

// 🔒 Verificación de sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'normal') {
    header("Location: ../index.php");
    exit;
}

require_once '../controllers/TaskController.php';
$taskController = new TaskController();

// 📝 Procesamiento de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_task'])) {
        $taskController->createTask($_SESSION['user_id'], $_POST['title'], $_POST['description'], $_POST['due_date']);
    }
    if (isset($_POST['update_status'])) {
        // 🔐 ÚNICO CAMBIO: Agregar validación de usuario
        $taskController->updateStatus($_POST['task_id'], $_POST['status'], $_SESSION['user_id']);
    }
}

// 📦 Obtener tareas del usuario
$tasks = $taskController->getTasksByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis tareas</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard_usuario.php">🔄 Actualizar</a> |
            <a href="../logout.php">🚪 Cerrar sesión</a>
        </div>

        <h2>Bienvenido <?= htmlspecialchars($_SESSION['username']) ?></h2>

        <!-- Sección de tareas existentes -->
        <div class="tasks-section">
            <h3>Tareas existentes</h3>
            <?php if (count($tasks) > 0): ?>
            <div class="task-list">
                <?php foreach ($tasks as $task): ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-title"><?= htmlspecialchars($task['title']) ?></div>
                            <div class="card-desc"><?= htmlspecialchars($task['description']) ?></div>
                            <div class="card-date">Vence: <?= $task['due_date'] ?></div>
                            <div class="card-status">Estado: <?= $task['status'] ?></div>
                        </div>
                        <form method="POST" class="card-form">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <select name="status">
                                <option value="pendiente" <?= $task['status'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="en progreso" <?= $task['status'] === 'en progreso' ? 'selected' : '' ?>>En progreso</option>
                                <option value="completada" <?= $task['status'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                            </select>
                            <button type="submit" name="update_status">Actualizar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p class="no-tasks">No tenés tareas registradas aún.</p>
            <?php endif; ?>
        </div>
        
        <!-- Sección para crear nueva tarea -->
        <div class="section section-highlight">
            <h3 class="section-title">Crear nueva tarea</h3>
            <form method="POST" class="new-task-form">
                <div class="form-group">
                    <label for="title">Título</label>
                    <input type="text" id="title" name="title" placeholder="Título de la tarea" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description" placeholder="Descripción de la tarea"></textarea>
                </div>
                <div class="form-group">
                    <label for="due_date">Fecha de vencimiento</label>
                    <input type="date" id="due_date" name="due_date" required>
                </div>
                <button type="submit" name="new_task" class="btn-create">Crear tarea</button>
            </form>
        </div>
    </div>
</body>
</html>