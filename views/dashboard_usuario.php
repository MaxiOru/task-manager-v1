<?php
session_start();

// 🔐 Verificación de sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'normal') {
    header("Location: login.php");
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
        $taskController->updateStatus($_POST['task_id'], $_POST['status']);
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
    <!-- <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2, h3 { margin-top: 30px; }
        .card {
            border: 1px solid #ccc;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            background-color: #f9f9f9;
        }
        form input, form textarea, form select, form button {
            margin-top: 6px;
            margin-bottom: 10px;
            display: block;
            width: 100%;
            max-width: 400px;
        }
        .nav {
            margin-bottom: 20px;
        }
    </style> -->
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard_usuario.php">🔄 Actualizar</a> |
            <a href="../logout.php">🚪 Cerrar sesión</a>
        </div>

        <h2>Bienvenido</h2>

        <form method="POST">
            <h3>Crear nueva tarea</h3>
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" id="title" name="title" placeholder="Título" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description" name="description" placeholder="Descripción"></textarea>
            </div>
            <div class="form-group">
                <label for="due_date">Fecha de vencimiento</label>
                <input type="date" id="due_date" name="due_date" required>
            </div>
            <button type="submit" name="new_task">Crear</button>
        </form>

        <h3>Tareas existentes</h3>
        <?php if (count($tasks) > 0): ?>
            <div class="task-list">
                <?php foreach ($tasks as $task): ?>
                    <div class="card">
                        <strong><?= htmlspecialchars($task['title']) ?></strong><br>
                        <?= htmlspecialchars($task['description']) ?><br>
                        Estado: <?= $task['status'] ?><br>
                        Vence: <?= $task['due_date'] ?><br>
                        <form method="POST">
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
            <p>No tenés tareas registradas aún.</p>
        <?php endif; ?>
    </div>
</body>
</html>