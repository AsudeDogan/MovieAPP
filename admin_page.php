<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Retrieve users
$user_sql = "SELECT * FROM users";
$user_result = $conn->query($user_sql);

// Retrieve genres and their counts (separate genres into individual rows)
$genre_sql = "SELECT genre FROM movies WHERE genre IS NOT NULL";
$genre_result = $conn->query($genre_sql);

// Retrieve total number of movies
$total_movies_sql = "SELECT COUNT(*) AS total_movies FROM movies";
$total_movies_result = $conn->query($total_movies_sql);
$total_movies = $total_movies_result->fetch_assoc()['total_movies'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #1f1f1f;
            padding: 20px;
            text-align: center;
        }
        header h1 {
            font-size: 36px;
        }
        .container {
            margin: 20px;
        }
        .section-title {
            font-size: 28px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #333;
        }
        th {
            background-color: #333;
        }
        tr:hover {
            background-color: #444;
        }
        .film-card {
            display: inline-block;
            width: 200px;
            margin: 15px;
            background: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .film-card:hover {
            transform: scale(1.05);
        }
        .film-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .film-card h4 {
            padding: 10px;
            margin: 0;
            background-color: #333;
            color: #fff;
        }
        button {
            background-color: #f1c40f; 
            color: black;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            font-size: 16px;
        }
        button:hover {
            background-color: #e67e22;
        }
        .logout-btn {
            background-color: #d32f2f;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #9a0007;
        }
        .actions a {
            color: #f1c40f; 
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <h1>Admin Page</h1>
</header>

<div class="container">
    <section>
        <h2 class="section-title">Users</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $user_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td class="actions">
                            <a href="change_role.php?id=<?php echo $user['id']; ?>&role=user">Make User</a> | 
                            <a href="change_role.php?id=<?php echo $user['id']; ?>&role=admin">Make Admin</a> | 
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2 class="section-title">Movies and Genres</h2>
        <p><strong>Total Movies: </strong><?php echo $total_movies; ?></p>
        <table>
            <thead>
                <tr>
                    <th>Genre</th>
                    <th>Number of Movies</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $genre_array = [];
                while ($genre = $genre_result->fetch_assoc()) {
                    $genre_list = explode(', ', $genre['genre']); // Split genres by commas
                    foreach ($genre_list as $single_genre) {
                        $genre_array[$single_genre] = isset($genre_array[$single_genre]) ? $genre_array[$single_genre] + 1 : 1;
                    }
                }
                foreach ($genre_array as $genre => $count): ?>
                    <tr>
                        <td><?php echo $genre; ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2 class="section-title">Movies</h2>
        <?php
        $movie_sql = "SELECT * FROM movies";
        $movie_result = $conn->query($movie_sql);
        while ($movie = $movie_result->fetch_assoc()):
        ?>
            <div class="film-card">
                <img src="<?php echo $movie['image_url']; ?>" alt="<?php echo $movie['title']; ?>">
                <h4><?php echo $movie['title']; ?></h4>
                <button onclick="window.location.href='edit_movie.php?id=<?php echo $movie['id']; ?>'">Edit</button>
                <button onclick="window.location.href='delete_movie.php?id=<?php echo $movie['id']; ?>'">Delete</button>
            </div>
        <?php endwhile; ?>
    </section>

    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
</div>

</body>
</html>
