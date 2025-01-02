<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MovieApp</title>
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
            padding: 10px 20px;
            background-color: #1c1c1c;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffb400;
        }
        .menu {
            display: flex;
            gap: 20px;
        }
        .menu a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .menu a:hover {
            color: #ffb400;
        }
        .welcome {
            text-align: center;
            margin-top: 80px; 
        }
        .movie-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 60px 20px; 
        }
        .movie-card {
            perspective: 1000px;
            width: 220px;
            height: 330px;
        }
        .movie-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }
        .movie-card:hover .movie-inner {
            transform: rotateY(180deg);
        }
        .movie-front, .movie-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 10px;
        }
        .movie-front img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
        }
        .movie-front h3 {
            margin: 10px 0 5px;
            color: white;
            font-size: 1.1rem;
        }
        .movie-front p {
            margin: 0;
            color: #aaa;
            font-size: 0.9rem;
        }
        .movie-back {
            background-color: #1c1c1c;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transform: rotateY(180deg);
        }
        .movie-back p {
            font-size: 0.9rem;
            color: white;
            text-align: center;
            padding: 10px;
        }
        .movie-back h3 {
            margin-top: 0;
            color: #ffb400;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">MovieApp</div>
    <nav class="menu">
        <a href="mood.php">Select Your Mood § Get Recommendation</a>
        <a href="plan.php">Plan a Movie Night</a>
        <a href="myMovies.php">My Movies</a>
        <a href="log.php">Log a Movie +</a>
        <a href="logout.php">Sign Out</a>
    </nav>
</header>

<div class="welcome">
    <h1>Welcome back, <?php echo ucfirst($_SESSION['username']); ?>!</h1>
    <p>Discover some Hidden Gems:</p>
</div>

<div class="movie-grid">
    <!-- Hidden Gems Movie 1 -->
    <div class="movie-card">
        <div class="movie-inner">
            <div class="movie-front">
                <img src="https://m.media-amazon.com/images/I/61Dbe0qBy9L.jpg" alt="Kill Bill Volume 1">
                <h3>Kill Bill Volume 1</h3>
                <p>(2003)</p>
            </div>
            <div class="movie-back">
                <h3>Kill Bill Volume 1</h3>
                <p>A former assassin wakes up from a coma and seeks revenge on those who betrayed her.</p>
            </div>
        </div>
    </div>

    <!--  Hidden Gems Movie 2 -->
    <div class="movie-card">
        <div class="movie-inner">
            <div class="movie-front">
                <img src="https://m.media-amazon.com/images/M/MV5BZGEyYzBiYmItZDM4OC00NTdmLWJlYzctODdiM2E2MjZmYTU2XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg" alt="The Worst Person in the World">
                <h3>The Worst Person in the World</h3>
                <p>(2021)</p>
            </div>
            <div class="movie-back">
                <h3>The Worst Person in the World</h3>
                <p>Julie navigates love, work, and identity in this poignant modern drama.</p>
            </div>
        </div>
    </div>

    <!--  Hidden Gems Movie 3 -->
    <div class="movie-card">
        <div class="movie-inner">
            <div class="movie-front">
                <img src="https://m.media-amazon.com/images/M/MV5BMjM0Nzk5NTc4OV5BMl5BanBnXkFtZTcwMDA2MzgxNA@@._V1_.jpg" alt="The Princess Bride">
                <h3>The Princess Bride</h3>
                <p>(1987)</p>
            </div>
            <div class="movie-back">
                <h3>The Princess Bride</h3>
                <p>A timeless tale of love, adventure, and wit set in a magical kingdom.</p>
            </div>
        </div>
    </div>

    <!--  Hidden Gems Movie 4 -->
    <div class="movie-card">
        <div class="movie-inner">
            <div class="movie-front">
                <img src="https://m.media-amazon.com/images/M/MV5BZDkzMzQ5ZmQtOTA3MC00MjhiLTk5M2UtNzk0MjEzZmVjN2UxXkEyXkFqcGc@._V1_.jpg" alt="Ponyo">
                <h3>Ponyo</h3>
                <p>(2008)</p>
            </div>
            <div class="movie-back">
                <h3>Ponyo</h3>
                <p>A magical fish befriends a young boy and dreams of becoming human.</p>
            </div>
        </div>
    </div>

    <!--  Hidden Gems Movie 5 -->
    <div class="movie-card">
        <div class="movie-inner">
            <div class="movie-front">
                <img src="https://m.media-amazon.com/images/M/MV5BZjNkY2M5NWMtZjJiMy00YTZmLWI2NWEtZTI0MjhmNDc4ZThmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg" alt="Fallen Leaves">
                <h3>Fallen Leaves</h3>
                <p>(2023)</p>
            </div>
            <div class="movie-back">
                <h3>Fallen Leaves</h3>
                <p>A tender story about two lonely people finding hope and love against the odds.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
