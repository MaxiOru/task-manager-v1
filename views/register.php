<?php
// Iniciar sesión
session_start();

// Redirigir si el usuario ya está logueado al dashboard correspondiente
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'jefe') {
        header("Location: dashboard_jefe.php");
    } else {
        header("Location: dashboard_usuario.php");
    }
    exit;
}

// Inicializar variables para mensajes
$success_message = "";
$error_message = "";

// Procesar formulario de registro cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/database.php';

    // Establecer conexión a la base de datos
    $db = new Database();
    $conn = $db->connect();

    // Obtener y limpiar datos del formulario
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Verificar si el nombre de usuario ya existe
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
    $checkStmt->execute(['username' => $username]);
    
    if ($checkStmt->fetch()) {
        $error_message = "El usuario ya existe. Elige otro nombre.";
    } else {
        // Crear nuevo usuario con contraseña encriptada
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar usuario con rol 'normal' y sin aprobar
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, approved) VALUES (:username, :password, 'normal', 0)");
        $result = $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword
        ]);

        // Verificar si el registro fue exitoso
        if ($result) {
            $success_message = "Registro exitoso. Espera aprobación del jefe.";
        } else {
            $error_message = "Error al registrar usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="container container-small">
        <h2>Registro de usuario</h2>
        
        <!-- Mostrar mensaje de éxito si el registro fue exitoso -->
        <?php if (!empty($success_message)): ?>
            <div class="message-center">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de error si hubo algún problema -->
        <?php if (!empty($error_message)): ?>
            <div class="message-center error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <!-- Mostrar formulario solo si no hubo registro exitoso -->
        <?php if (empty($success_message)): ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Usuario" required><br>
                <input type="password" name="password" placeholder="Contraseña" required><br>
                <button type="submit">Registrarse</button>
            </form>
        <?php endif; ?>
        
        <!-- Enlace para ir al login -->
        <p >
            ¿Ya tienes cuenta? <a href="../index.php">Iniciar Sesión</a>
        </p>
    </div>
</body>
</html>