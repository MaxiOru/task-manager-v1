<?php
// Iniciar sesión solo si no hay una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir usuarios ya logueados al dashboard correspondiente según su rol
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'jefe') {
        header("Location: views/dashboard_jefe.php");
    } else {
        header("Location: views/dashboard_usuario.php");
    }
    exit;
}

// Inicializar variable para mensajes de error
$error_message = "";

// Procesar formulario de login cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'controllers/AuthController.php';
    
    // Crear instancia del controlador de autenticación
    $authController = new AuthController();
    $result = $authController->login($_POST['username'], $_POST['password']);
    
    // Si el resultado es un string, significa que hubo un error
    if (is_string($result)) {
        $error_message = $result;
    }
    // Si no es string, el login fue exitoso y la redirección se maneja en el controlador
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
    <div class="container container-small" >
        <h2>Iniciar Sesión</h2>
        
        <!-- Mostrar mensaje de error si hay problemas en el login -->
        <?php if (!empty($error_message)): ?>
            <div >
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de inicio de sesión -->
        <form method="POST">
            <input type="text" name="username" placeholder="Usuario" required><br>
            <input type="password" name="password" placeholder="Contraseña" required><br>
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <!-- Enlace para ir al registro -->
        <p >
            ¿No tienes cuenta? <a href="views/register.php">Registrarse</a>
        </p>
    </div>
</body>
</html>