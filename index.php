<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado, redirigir según su rol
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'jefe') {
        header("Location: views/dashboard_jefe.php");
    } else {
        header("Location: views/dashboard_usuario.php");
    }
    exit;
}

$error_message = "";

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'controllers/AuthController.php';
    
    $authController = new AuthController();
    $result = $authController->login($_POST['username'], $_POST['password']);
    
    // Si login devuelve un string, es un error
    if (is_string($result)) {
        $error_message = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Login</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto;">
        <h2>Iniciar Sesión</h2>
        
        <?php if (!empty($error_message)): ?>
            <div style="color: red; margin-bottom: 15px;">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Usuario" required><br>
            <input type="password" name="password" placeholder="Contraseña" required><br>
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <p style="margin-top: 20px; text-align: center;">
            ¿No tienes cuenta? <a href="register.php">Registrarse</a>
        </p>
    </div>
</body>
</html>