<?php
// Oturum başlatma
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>User ID not set in session. Please log in again.</p>";
    exit();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Veritabanı bağlantısını dahil et
include 'db_connection.php';

// Kullanıcıya gösterilecek mesaj için değişken
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = $_POST['movie_id'] ?? null; // POST isteğinden film ID'sini al
    $rating = $_POST['rating'] ?? null; // POST isteğinden rating (star) verisini al

    if ($movie_id) {
        // Kullanıcının oturum açmış ID'sini al
        $user_id = $_SESSION['user_id'];

        // Daha önce eklenmiş mi kontrol et
        $check_query = "SELECT * FROM logged_movies WHERE user_id = ? AND movie_id = ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param('ii', $user_id, $movie_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $message = "<p style='color: red;'>This movie is already logged in your library.</p>";
        } else {
            // Daha önce eklenmemişse ekle
            $query = "INSERT INTO logged_movies (user_id, movie_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param('ii', $user_id, $movie_id);

                if ($stmt->execute()) {
                    // Film başarıyla eklendiğinde rating'i kaydet
                    if ($rating) {
                        $rating_query = "INSERT INTO ratings (user_id, movie_id, rating) VALUES (?, ?, ?)
                                         ON DUPLICATE KEY UPDATE rating = ?";
                        $stmt_rating = $conn->prepare($rating_query);
                        $stmt_rating->bind_param('iiii', $user_id, $movie_id, $rating, $rating);
                        $stmt_rating->execute();
                        $stmt_rating->close();
                    }
                    // Film ve rating başarıyla eklendiğinde myMovies.php sayfasına yönlendirme
                    header("Location: myMovies.php");
                    exit();
                } else {
                    $message = "<p style='color: red;'>Error adding movie to your library: " . htmlspecialchars($stmt->error) . "</p>";
                }

                $stmt->close();
            } else {
                $message = "<p style='color: red;'>Error preparing the statement: " . htmlspecialchars($conn->error) . "</p>";
            }
        }

        $stmt_check->close();
    } else {
        // Eğer film ID'si seçilmezse hata mesajı göster
        $message = "<p style='color: red;'>No movie selected. Please try again.</p>";
    }
}

// Veritabanı bağlantısını kapat
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log a Movie</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        input, button {
            margin: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }
        input {
            width: 300px;
        }
        button {
            background-color: #ffb400;
            color: black;
            cursor: pointer;
        }
        button:hover {
            background-color: #ffa000;
        }
        #suggestions {
            color: white;
            background: #1c1c1c;
            border: 1px solid #333;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            top: 60px;
            width: 300px;
            z-index: 1000;
        }
        #suggestions div {
            padding: 10px;
            cursor: pointer;
        }
        #suggestions div:hover {
            background-color: #333;
        }
        a {
            color: white;
            text-decoration: none;
            margin-top: 20px;
        }
        a:hover {
            text-decoration: underline;
        }
        .rating {
            direction: rtl;
            unicode-bidi: bidi-override;
            font-size: 30px;
        }
        .rating input {
            display: none;
        }
        .rating label {
            color: gray;
            cursor: pointer;
        }
        .rating input:checked ~ label {
            color: gold;
        }
    </style>
</head>
<body>
    <h1>Log a Movie</h1>
    <?= $message; ?> <!-- Kullanıcıya gösterilecek mesaj -->
    <form method="POST" id="logMovieForm">
        <input type="text" id="movieInput" name="movie_name" placeholder="Enter movie name..." autocomplete="off" required>
        <div id="suggestions"></div>
        <input type="hidden" id="selectedMovieId" name="movie_id"> <!-- Seçilen filmin ID'si -->
        
        <!-- Star Rating UI -->
       
        <div class="rating">
            <input type="radio" name="rating" value="5" id="star5" />
            <label for="star5">☆</label>
            <input type="radio" name="rating" value="4" id="star4" />
            <label for="star4">☆</label>
            <input type="radio" name="rating" value="3" id="star3" />
            <label for="star3">☆</label>
            <input type="radio" name="rating" value="2" id="star2" />
            <label for="star2">☆</label>
            <input type="radio" name="rating" value="1" id="star1" />
            <label for="star1">☆</label>
        </div>
        
        <button type="submit">Search & Add</button>
    </form>
    <a href="hosgeldin.php">Go Back Home</a>

    <script>
        const movieInput = document.getElementById('movieInput');
        const suggestionsDiv = document.getElementById('suggestions');
        const selectedMovieId = document.getElementById('selectedMovieId');

        movieInput.addEventListener('input', () => {
            const query = movieInput.value.trim();
            if (query.length < 2) {
                suggestionsDiv.innerHTML = '';
                return;
            }
            fetch(`autocomplete.php?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsDiv.innerHTML = '';
                    if (data.length === 0) {
                        suggestionsDiv.innerHTML = '<div>No results found</div>';
                        return;
                    }
                    data.forEach(movie => {
                        const suggestion = document.createElement('div');
                        suggestion.textContent = movie.title; // Sadece film adı gösteriliyor
                        suggestion.dataset.movieId = movie.id;
                        suggestion.addEventListener('click', () => {
                            movieInput.value = movie.title;
                            selectedMovieId.value = movie.id; 
                            suggestionsDiv.innerHTML = '';
                        });
                        suggestionsDiv.appendChild(suggestion);
                    });
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                });
        });
    </script>
</body>
</html>
