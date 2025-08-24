<?php
// Iniciar sesión
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté logueado y tenga rol de jefe
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jefe') {
    header("Location: ../index.php");
    exit;
}

// Establecer conexión a la base de datos
$db = new Database();
$conn = $db->connect();

// Obtener usuarios normales pendientes de aprobación (no rechazados)
$stmtPendientes = $conn->query("SELECT * FROM users WHERE role = 'normal' AND approved = 0 AND rejected = 0");
$pendientes = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios normales ya aprobados
$stmtAprobados = $conn->query("SELECT * FROM users WHERE role = 'normal' AND approved = 1");
$aprobados = $stmtAprobados->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios normales rechazados
$stmtRechazados = $conn->query("SELECT * FROM users WHERE role = 'normal' AND rejected = 1");
$rechazados = $stmtRechazados->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las tareas del sistema con información del usuario asignado
$stmtTasks = $conn->query("
    SELECT t.*, u.username 
    FROM tasks t 
    LEFT JOIN users u ON t.user_id = u.id 
    ORDER BY t.due_date ASC, t.status ASC
");
$allTasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

// Inicializar contadores para estadísticas de tareas
$taskStats = [
    'pendiente' => 0,
    'en progreso' => 0,
    'completada' => 0
];

// Contar tareas por cada estado
foreach ($allTasks as $task) {
    if (isset($taskStats[$task['status']])) {
        $taskStats[$task['status']]++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Jefe - Administración</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Barra de navegación -->
        <div class="nav">
            <a href="dashboard_jefe.php">📄 Actualizar</a> |
            <a href="../logout.php">🚪 Cerrar sesión</a>
        </div>

        <h2>Panel de Administración - Bienvenido, Jefe</h2>

        <!-- Mostrar mensaje de éxito si se aprobó un usuario -->
        <?php if (isset($_GET['aprobado']) && $_GET['aprobado'] == 1): ?>
            <div class="message success">✅ Usuario aprobado correctamente.</div>
        <?php endif; ?>

        <!-- Sección de estadísticas de tareas -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $taskStats['pendiente'] ?></div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $taskStats['en progreso'] ?></div>
                <div class="stat-label">En Progreso</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $taskStats['completada'] ?></div>
                <div class="stat-label">Completadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($allTasks) ?></div>
                <div class="stat-label">Total Tareas</div>
            </div>
        </div>

        <!-- Tabla con todas las tareas del sistema -->
        <div class="section">
            <h3>📋 Todas las Tareas del Sistema</h3>
            <?php if (count($allTasks) > 0): ?>
                <table>
                    <tr>
                        <th>Usuario</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Fecha Vencimiento</th>
                    </tr>
                    <?php foreach ($allTasks as $task): ?>
                        <?php
                        // Determinar el estilo de fecha según si está vencida, es hoy o futura
                        $today = date('Y-m-d');
                        $dueDate = $task['due_date'];
                        $dateClass = 'date-future';
                        if ($dueDate < $today) {
                            $dateClass = 'date-overdue';
                        } elseif ($dueDate == $today) {
                            $dateClass = 'date-today';
                        }
                        $formattedDate = date('d/m/Y', strtotime($dueDate));
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($task['username']) ?></strong></td>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <!-- Limitar descripción a 50 caracteres -->
                            <td><?= htmlspecialchars(substr($task['description'], 0, 50)) ?><?= strlen($task['description']) > 50 ? '...' : '' ?></td>
                            <td>
                                <!-- Aplicar clase CSS según el estado de la tarea -->
                                <span class="status status-<?= str_replace(' ', '-', $task['status']) == 'en-progreso' ? 'progreso' : str_replace(' ', '-', $task['status']) ?>">
                                    <?= ucfirst($task['status']) ?>
                                </span>
                            </td>
                            <td class="<?= $dateClass ?>">
                                <?= $formattedDate ?>
                                <!-- Mostrar etiqueta adicional para fechas especiales -->
                                <?php if ($dueDate < $today): ?>
                                    (Vencida)
                                <?php elseif ($dueDate == $today): ?>
                                    (Hoy)
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No hay tareas en el sistema aún.</p>
            <?php endif; ?>
        </div>

        <!-- Sección de usuarios pendientes de aprobación -->
        <div class="section">
            <h3>👥 Usuarios pendientes de aprobación</h3>
            <?php if (count($pendientes) > 0): ?>
                <table>
                    <tr>
                        <th>Usuario</th>
                        <th>Fecha Registro</th>
                        <th>Acción</th>
                    </tr>
                    <?php foreach ($pendientes as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= isset($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : 'Sin fecha' ?></td>
                            <td>
                                <!-- Formulario para aprobar usuario -->
                                <form method="POST" action="../approve_user.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit">Aprobar</button>
                                </form>
                                <!-- Formulario para rechazar usuario -->
                                <form method="POST" action="../reject_user.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" style="background:#dc3545;color:#fff;">Rechazar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="message success">✅ No hay usuarios pendientes de aprobación.</div>
            <?php endif; ?>
        </div>

        <!-- Lista de usuarios ya aprobados -->
        <div class="section">
            <h3>✅ Usuarios aprobados</h3>
            <?php if (count($aprobados) > 0): ?>
                <ul>
                    <?php foreach ($aprobados as $user): ?>
                        <li>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay usuarios aprobados aún.</p>
            <?php endif; ?>
        </div>

        <!-- Lista de usuarios rechazados -->
        <div class="section">
            <h3>❌ Usuarios rechazados</h3>
            <?php if (count($rechazados) > 0): ?>
                <ul>
                    <?php foreach ($rechazados as $user): ?>
                        <li>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay usuarios rechazados aún.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>