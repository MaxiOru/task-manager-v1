<?php
session_start();
require_once '../config/database.php';

// Verificación de sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jefe') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
$db = new Database();
$conn = $db->connect();

// Usuarios pendientes de aprobación
$stmtPendientes = $conn->query("SELECT * FROM users WHERE role = 'normal' AND approved = 0");
$pendientes = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

// Usuarios ya aprobados
$stmtAprobados = $conn->query("SELECT * FROM users WHERE role = 'normal' AND approved = 1");
$aprobados = $stmtAprobados->fetchAll(PDO::FETCH_ASSOC);

// NUEVA FUNCIONALIDAD: Obtener todas las tareas con información del usuario
$stmtTasks = $conn->query("
    SELECT t.*, u.username 
    FROM tasks t 
    LEFT JOIN users u ON t.user_id = u.id 
    ORDER BY t.due_date ASC, t.status ASC
");
$allTasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

// Contar tareas por estado
$taskStats = [
    'pendiente' => 0,
    'en progreso' => 0,
    'completada' => 0
];

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
    <!-- <style>
        /* ===== ESTILOS BASE ===== */
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f8f9fa;
            color: #333;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h2, h3 { margin-top: 40px; }

        /* ===== UTILIDADES ===== */
        .card, .section, .nav, table {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }

        /* ===== NAV ===== */
        .nav { padding: 15px; margin-bottom: 20px; }
        .nav a {
            text-decoration: none;
            margin-right: 15px;
            color: #007BFF;
            font-weight: bold;
        }
        .nav a:hover { text-decoration: underline; }

        /* ===== ESTADÍSTICAS ===== */
        .stats {
            display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px;
        }
        .stat-card { flex: 1; min-width: 150px; padding: 20px; }
        .stat-number { font-size: 2em; font-weight: bold; color: #007BFF; }
        .stat-label { color: #666; text-transform: uppercase; font-size: 0.9em; }

        /* ===== TABLAS ===== */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; overflow: hidden; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #007BFF; color: #fff; }
        tr:nth-child(even) { background: #f8f9fa; }
        tr:hover { background: #e3f2fd; }

        /* ===== ESTADOS ===== */
        .status { padding: 4px 12px; border-radius: 15px; font-size: 0.85em; font-weight: bold; }
        .status-pendiente { background: #fff3cd; color: #856404; }
        .status-progreso { background: #cce5ff; color: #004085; }
        .status-completada { background: #d4edda; color: #155724; }

        /* ===== FECHAS ===== */
        .date-overdue { color: #dc3545; font-weight: bold; }
        .date-today   { color: #fd7e14; font-weight: bold; }
        .date-future  { color: #28a745; }

        /* ===== BOTONES ===== */
        button { 
            padding: 8px 16px; border: none; border-radius: 4px;
            background: #28a745; color: #fff; cursor: pointer;
        }
        button:hover { background: #218838; }

        /* ===== MENSAJES Y SECCIONES ===== */
        .mensaje { 
            color: green; margin-top: 10px; padding: 10px;
            background: #d4edda; border-radius: 4px;
        }
        .section { padding: 20px; margin-bottom: 30px; }

    </style> -->
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard_jefe.php">🔄 Actualizar</a> |
            <a href="../logout.php">🚪 Cerrar sesión</a>
        </div>

        <h2>Panel de Administración - Bienvenido, Jefe</h2>

        <?php if (isset($_GET['aprobado']) && $_GET['aprobado'] == 1): ?>
            <div class="message success">✅ Usuario aprobado correctamente.</div>
        <?php endif; ?>

        <!-- Estadísticas de tareas -->
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

        <!-- Todas las tareas -->
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
                            <td><?= htmlspecialchars(substr($task['description'], 0, 50)) ?><?= strlen($task['description']) > 50 ? '...' : '' ?></td>
                            <td>
                                <span class="status status-<?= str_replace(' ', '-', $task['status']) == 'en-progreso' ? 'progreso' : str_replace(' ', '-', $task['status']) ?>">
                                    <?= ucfirst($task['status']) ?>
                                </span>
                            </td>
                            <td class="<?= $dateClass ?>">
                                <?= $formattedDate ?>
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

        <!-- Usuarios pendientes de aprobación -->
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
                            <td>Pendiente</td>
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
                <div class="message success">✅ No hay usuarios pendientes de aprobación.</div>
            <?php endif; ?>
        </div>

        <!-- Usuarios aprobados -->
        <div class="section">
            <h3>✅ Usuarios aprobados</h3>
            <?php if (count($aprobados) > 0): ?>
                <ul>
                    <?php foreach ($aprobados as $user): ?>
                        <li>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                            <span class="status status-completada" style="margin-left: 10px;">● Activo</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay usuarios aprobados aún.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

