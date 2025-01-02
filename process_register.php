<?php
// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "movie_recommendation");

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Kayıt işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Şifreyi hashle
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kullanıcıyı veritabanına ekle
    $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: login.php"); // Kayıt sonrası login sayfasına yönlendir
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
