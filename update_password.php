<?php
// Veritabanı bağlantısı
include('db_connection.php');

// Hash'lenmiş şifreyi oluşturma
$new_password = password_hash('123', PASSWORD_DEFAULT);  // '123' yerine admin'in şifresini kullanabilirsiniz

// Admin kullanıcısının şifresini güncelleme
$sql = "UPDATE users SET password = ? WHERE username = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $new_password);
$stmt->execute();

echo "Admin şifresi başarıyla güncellendi.";
?>
