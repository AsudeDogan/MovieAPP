<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// ID ve yeni rol parametrelerini al
$user_id = $_GET['id'];
$new_role = $_GET['role'];

// Veritabanında kullanıcıyı güncelle
$sql = "UPDATE users SET role = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_role, $user_id);
$stmt->execute();

header("Location: admin_page.php");
exit();
