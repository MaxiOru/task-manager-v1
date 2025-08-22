<?php
session_start();
require_once '../config/database.php';

// 🔐 Verificación de sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jefe') {
    header("Location: login.php");
    exit;
}

// 📦 Conexión a la base de datos
$db = new Database();
$conn = $db->connect();

// 🔍 Usuarios pendientes de aprobación
$stmtPendientes = $conn->query("SELECT * FROM users WHERE role = 'normal' AND approved = 0");
$pendientes = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

// ✅ Usuarios ya aprobados (opcional)
$stmtAprobados = $conn->query("SELECT * FROM users WHERE role = 'normal' AND approved = 1");
$aprobados = $stmtAprobados->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Jefe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h3 { margin-top: 40px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        button { padding: 6px 12px; }
        .mensaje { color: green; margin-top: 10px; }
        .nav { margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="nav">
        <a href="dashboard_jefe.php">🔄 Actualizar</a> |
        <a href="../logout.php">🚪 Cerrar sesión</a>
    </div>

    <h2>Bienvenido, Jefe</h2>

    <?php if (isset($_GET['aprobado']) && $_GET['aprobado'] == 1): ?>
        <p class="mensaje">✅ Usuario aprobado correctamente.</p>
    <?php endif; ?>

    <h3>Usuarios pendientes de aprobación</h3>
    <?php if (count($pendientes) > 0): ?>
        <table>
            <tr>
                <th>Usuario</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($pendientes as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td>
                        <form method="POST" action="../approve_user.php">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit">Aprobar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay usuarios pendientes.</p>
    <?php endif; ?>

    <h3>Usuarios ya aprobados</h3>
    <?php if (count($aprobados) > 0): ?>
        <ul>
            <?php foreach ($aprobados as $user): ?>
                <li><?= htmlspecialchars($user['username']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay usuarios aprobados aún.</p>
    <?php endif; ?>

</body>
</html>