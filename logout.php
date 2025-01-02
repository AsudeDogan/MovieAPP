<?php
session_start(); // Oturumu başlat
session_destroy(); // Tüm oturum verilerini sil
header("Location: index.php"); // Ana sayfaya yönlendir
exit();
?>
