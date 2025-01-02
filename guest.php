<?php
// Misafir ekranı için veritabanı bağlantısı
$conn = new mysqli('localhost', 'root', '', 'movie_recommendation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Tüm filmleri çek
$query = "SELECT title, image_url FROM movies ORDER BY title ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest - Movie Night</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #1c1c1c;
        }
        header h1 {
            margin: 0;
            font-size: 2.2rem;
            color: white;
        }
        header h1 span {
            color: #ffb400;
        }
        .home-btn {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #444;
            border-radius: 5px;
            font-size: 1rem;
        }
        .home-btn:hover {
            background-color: #666;
        }
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            justify-content: center;
            margin: 20px;
        }
        .movie-card {
            background-color: #1c1c1c;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .movie-card:hover {
            transform: scale(1.05);
        }
        .movie-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .movie-card h3 {
            font-size: 0.9rem;
            margin: 10px 0;
            color: #ffb400;
        }
    </style>
</head>
<body>
    <!-- Header ve Ana Sayfa Butonu -->
    <header>
        <h1><span>Guest View</span></h1>
        <a href="login.php" class="home-btn">Go to Homepage</a>
    </header>

    <p style="margin-top: 20px;">Explore Our Movie Collection</p>

    <!-- Film Listesi -->
    <div class="movie-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="movie-card">
                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Movie Poster">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
