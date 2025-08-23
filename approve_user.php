<?php
session_start();

// Verificar que es jefe
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jefe') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    
    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("UPDATE users SET approved = 1 WHERE id = :id");
    $stmt->execute(['id' => $_POST['user_id']]);

    header("Location: views/dashboard_jefe.php?aprobado=1");
    exit;
}
?>