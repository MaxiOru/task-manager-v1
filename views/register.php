<?php
session_start();

// Si ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'jefe') {
        header("Location: views/dashboard_jefe.php");
    } else {
        header("Location: views/dashboard_usuario.php");
    }
    exit;
}

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // require_once '/../config/database.php';
    require_once __DIR__ . '/../config/database.php';

    $db = new Database();
    $conn = $db->connect();

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Verificar si el usuario ya existe
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
    $checkStmt->execute(['username' => $username]);
    
    if ($checkStmt->fetch()) {
        $error_message = "El usuario ya existe. Elige otro nombre.";
    } else {
        // Crear usuario
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, approved) VALUES (:username, :password, 'normal', 0)");
        $result = $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword
        ]);

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
        
        <?php if (!empty($success_message)): ?>
            <div>
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div >
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($success_message)): ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Usuario" required><br>
                <input type="password" name="password" placeholder="Contraseña" required><br>
                <button type="submit">Registrarse</button>
            </form>
        <?php endif; ?>
        
        <p >
            ¿Ya tienes cuenta? <a href="../index.php">Iniciar Sesión</a>
        </p>
    </div>
</body>
</html>