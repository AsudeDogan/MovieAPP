<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Silinecek kullanıcının ID'sini al
$user_id = $_GET['id'];

// Kullanıcıyı veritabanından sil
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: admin_page.php");
exit();
