<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Veritabanı bağlantısı kontrolü
$conn = new mysqli("localhost", "root", "", "movie_recommendation");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Film ID'sini al
if (isset($_GET['id'])) {
    $movie_id = $_GET['id'];

    // Film bilgilerini veritabanından al
    $sql = "SELECT * FROM movies WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie = $result->fetch_assoc();
    
    if (!$movie) {
        echo "Movie not found!";
        exit();
    }

    // Eğer form gönderildiyse, güncelleme işlemini yap
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $genres = implode(", ", $_POST['genre']); // Seçilen genre'leri birleştir
        $image_url = $_POST['image_url'];

        // Film bilgilerini güncelle
        $update_sql = "UPDATE movies SET title = ?, genre = ?, image_url = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $title, $genres, $image_url, $movie_id);
        
        if ($update_stmt->execute()) {
            echo "Movie updated successfully!";
            header("Location: admin_page.php");
            exit();
        } else {
            echo "Error updating movie!";
        }

        $update_stmt->close();
    }
} else {
    echo "Invalid movie ID!";
}

// Veritabanındaki mevcut türleri al (yeni türler ekleyebiliriz)
$genres = ["Action", "Comedy", "Drama", "Horror", "Romance","Adventure","Animation", "Comforting"];

$conn->close(); // Veritabanı bağlantısını burada kapatıyoruz
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            padding: 30px;
            background-color: #1f1f1f;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border-radius: 5px;
            border: 1px solid #333;
        }
        button {
            background-color: #f1c40f; 
            color: black;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #e67e22;
        }
        .go-back-btn {
            background-color:rgb(158, 150, 150); 
            margin-top: 10px;
        }
        .go-back-btn:hover {
            background-color:rgb(180, 173, 173);
        }
        .checkbox-group {
            margin-top: 10px;
            display: flex;
            gap: 15px; 
            flex-wrap: wrap;
        }
        .checkbox-group label {
            font-size: 14px; 
            padding: 5px;
            background-color: #333;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .checkbox-group input {
            transform: scale(1.1); 
            margin-right: 5px; 
        }
        .checkbox-group label:hover {
            background-color: #444; 
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Movie</h2>
    <form method="POST" action="">
        <input type="text" name="title" placeholder="Title" value="<?php echo $movie['title']; ?>" required>

        <label for="genre">Select Genre</label>
        <div class="checkbox-group">
            <?php foreach ($genres as $genre): ?>
                <label>
                    <input type="checkbox" name="genre[]" value="<?php echo $genre; ?>" 
                        <?php echo (in_array($genre, explode(", ", $movie['genre']))) ? 'checked' : ''; ?>>
                    <?php echo $genre; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <input type="text" name="image_url" placeholder="Image URL" value="<?php echo $movie['image_url']; ?>" required>
        
        <button type="submit">Update Movie</button>
    </form>
    
    <button class="go-back-btn" onclick="window.location.href='admin_page.php'">Go back to Admin Page</button>
</div>

</body>
</html>
