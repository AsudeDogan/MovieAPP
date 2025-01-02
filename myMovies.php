<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

// Filmleri çek
$user_id = $_SESSION['user_id'];
$query = "SELECT movies.id, movies.title, movies.image_url, ratings.rating 
          FROM movies
          INNER JOIN logged_movies ON movies.id = logged_movies.movie_id
          LEFT JOIN ratings ON movies.id = ratings.movie_id AND ratings.user_id = ?
          WHERE logged_movies.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}
$stmt->close();

// Silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_movies'])) {
    if (isset($_POST['selected_movies']) && is_array($_POST['selected_movies'])) {
        $movie_ids = implode(",", array_map('intval', $_POST['selected_movies']));
        $delete_query = "DELETE FROM logged_movies WHERE user_id = ? AND movie_id IN ($movie_ids)";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param('i', $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        header("Location: myMovies.php"); // Sayfayı yenile
        exit();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Movies</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 0;
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
            color: #ffb400; 
        }
        .remove-btn {
            background-color: #ff4c4c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .remove-btn:hover {
            background-color: #ff2a2a;
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
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 20px;
        }
        .movie-card {
            background-color: #1c1c1c;
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            padding: 10px;
        }
        .movie-card img {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
        .movie-card h3 {
            font-size: 1rem;
            color: #ffb400; 
            margin: 0;
        }
        .movie-rating {
            color: gold;
            font-size: 20px;
        }
        .checkbox {
            margin-top: 10px;
            display: none;
        }
    </style>
    <script>
        function toggleCheckboxes() {
            const checkboxes = document.querySelectorAll('.checkbox');
            const deleteButton = document.getElementById('deleteButton');
            const currentState = checkboxes[0]?.style.display === 'block';
            checkboxes.forEach(checkbox => checkbox.style.display = currentState ? 'none' : 'block');
            deleteButton.style.display = currentState ? 'none' : 'block';
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete the selected movies?");
        }
    </script>
</head>
<body>
<header>
    <h1>My Movies</h1>
    <div>
        <button class="remove-btn" onclick="toggleCheckboxes()" type="button">Remove Movie</button>
        <a href="hosgeldin.php" class="home-btn">Go to Homepage</a>
    </div>
</header>

<form method="POST" onsubmit="return confirmDelete();">
    <div class="movie-grid">
        <?php if (!empty($movies)): ?>
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <img src="<?php echo htmlspecialchars($movie['image_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <h3><?php echo htmlspecialchars($movie['title']); ?></h3>

                    <!-- Display Rating -->
                    <div class="movie-rating">
                        <?php 
                            $rating = $movie['rating'] ?? 0; // If no rating, default to 0
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo "★"; // Filled star for rating
                                } else {
                                    echo "☆"; // Empty star
                                }
                            }
                        ?>
                    </div>

                    <div class="checkbox">
                        <input type="checkbox" name="selected_movies[]" value="<?php echo $movie['id']; ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">You haven't added any movies yet.</p>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin: 20px;">
        <button type="submit" name="delete_movies" id="deleteButton" class="remove-btn" style="display: none;">Delete Selected</button>
    </div>
</form>
</body>
</html>
