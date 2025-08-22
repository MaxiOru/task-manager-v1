<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("UPDATE users SET approved = 1 WHERE id = :id");
    $stmt->execute(['id' => $_POST['user_id']]);

    header("Location: views/dashboard_jefe.php");
}