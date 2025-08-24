<?php
// Iniciar sesión
session_start();

// Verificar que el usuario logueado sea jefe (solo jefes pueden aprobar usuarios)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jefe') {
    header("Location: index.php");
    exit;
}

// Procesar solicitud de aprobación cuando se recibe POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    
    // Establecer conexión a la base de datos
    $db = new Database();
    $conn = $db->connect();

    // Actualizar el campo 'approved' del usuario a 1 (aprobado)
    $stmt = $conn->prepare("UPDATE users SET approved = 1 WHERE id = :id");
    $stmt->execute(['id' => $_POST['user_id']]);

    // Redirigir al dashboard del jefe con mensaje de confirmación
    header("Location: views/dashboard_jefe.php?aprobado=1");
    exit;
}
?>