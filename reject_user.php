<?php
// Iniciar sesión
session_start();

// Verificar que el usuario logueado sea jefe (solo jefes pueden rechazar usuarios)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jefe') {
    header("Location: index.php");
    exit;
}

// Procesar solicitud de rechazo cuando se recibe POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    
    // Establecer conexión a la base de datos
    $db = new Database();
    $conn = $db->connect();

    // Actualizar el campo 'rejected' del usuario a 1 (rechazado)
    $stmt = $conn->prepare("UPDATE users SET rejected = 1 WHERE id = :id");
    $stmt->execute(['id' => $_POST['user_id']]);

    // Redirigir al dashboard del jefe sin parámetros adicionales
    header("Location: views/dashboard_jefe.php");
    exit;
}
?>