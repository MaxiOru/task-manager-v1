<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'normal') {
    header("Location: login.php");
    exit;
}

require_once '../controllers/TaskController.php';

$taskController = new TaskController();
$success_message = "";
$error_message = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    
    // Validaciones básicas
    if (empty($title)) {
        $error_message = "El título es obligatorio.";
    } elseif (empty($due_date)) {
        $error_message = "La fecha de vencimiento es obligatoria.";
    } elseif (strtotime($due_date) < strtotime(date('Y-m-d'))) {
        $error_message = "La fecha de vencimiento no puede ser anterior a hoy.";
    } else {
        try {
            $taskController->createTask($_SESSION['user_id'], $title, $description, $due_date);
            $success_message = "Tarea creada exitosamente.";
            
            // Limpiar formulario después del éxito
            $title = $description = $due_date = "";
        } catch (Exception $e) {
            $error_message = "Error al crear la tarea. Intenta nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Tarea</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <!-- <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .nav a {
            text-decoration: none;
            margin-right: 15px;
            color: #007BFF;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        form input, form textarea, form button {
            width: 100%;
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        form textarea {
            resize: vertical;
            height: 100px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style> -->
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard_usuario.php">← Volver al Dashboard</a> |
            <a href="../logout.php">🚪 Cerrar Sesión</a>
        </div>

        <h2>Crear Nueva Tarea</h2>

        <?php if (!empty($success_message)): ?>
            <div class="message success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="title">Título de la tarea *</label>
                <input type="text" 
                    id="title" 
                    name="title" 
                    placeholder="Ej: Revisar documentación del proyecto" 
                    value="<?= isset($title) ? htmlspecialchars($title) : '' ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description" 
                        name="description" 
                        placeholder="Describe los detalles de la tarea (opcional)"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label for="due_date">Fecha de vencimiento *</label>
                <input type="date" 
                    id="due_date" 
                    name="due_date" 
                    value="<?= isset($due_date) ? htmlspecialchars($due_date) : '' ?>"
                    min="<?= date('Y-m-d') ?>"
                    required>
            </div>

            <button type="submit">Crear Tarea</button>
        </form>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <p>¿Ya terminaste? <a href="dashboard_usuario.php">Ver todas mis tareas</a></p>
        </div>
    </div>
</body>
</html>