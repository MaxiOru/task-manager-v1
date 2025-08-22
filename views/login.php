<?php
session_start();

// Si ya está logueado, redirigir según su rol
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'jefe') {
        header("Location: dashboard_jefe.php");
    } else {
        header("Location: dashboard_usuario.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto;">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="../index.php">
            <input type="text" name="username" placeholder="Usuario" required><br>
            <input type="password" name="password" placeholder="Contraseña" required><br>
            <button type="submit">Entrar</button>
        </form>
        
        <p style="margin-top: 20px; text-align: center;">
            ¿No tienes cuenta? <a href="../register.php">Registrarse</a>
        </p>
    </div>
</body>
</html>