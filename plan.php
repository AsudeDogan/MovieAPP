<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'movie_recommendation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fetch all movies
$query = "SELECT title, image_url FROM movies ORDER BY title ASC";
$result = $conn->query($query);

// fetch user's friends to invite
$friends_query = "SELECT username FROM users WHERE id != ?"; // Exclude the logged-in user
$friends_stmt = $conn->prepare($friends_query);
$friends_stmt->bind_param('i', $_SESSION['user_id']);
$friends_stmt->execute();
$friends_result = $friends_stmt->get_result();
$friends = [];
while ($friend = $friends_result->fetch_assoc()) {
    $friends[] = $friend['username'];
}
$friends_stmt->close();

// film seçildiğinde mesaj göstermek için
$selected_movie = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['selected_movie']) || empty($_POST['selected_movie'])) {
        echo "<script>alert('Film seçmediniz. Lütfen bir film seçin!');</script>";
    } elseif (isset($_POST['selected_time']) && !empty($_POST['selected_time'])) {
        $selected_movie = htmlspecialchars($_POST['selected_movie']);
        $selected_time = htmlspecialchars($_POST['selected_time']);
        $selected_friends = isset($_POST['friends']) ? $_POST['friends'] : [];

        // save event details into the database (This part is demo.)
        echo "<script>alert('Etkinlik planlandı: \"$selected_movie\" \\nTarih ve Saat: $selected_time\\n\\nBu özellik henüz demo aşamasındadır.');</script>";

        // show invited friends
        if (!empty($selected_friends)) {
            echo "<script>alert('Davet edilen arkadaşlar: " . implode(", ", $selected_friends) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan a Movie Night</title>
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
        .form-container {
            margin: 20px auto;
            padding: 20px;
            background-color: #1c1c1c;
            border-radius: 8px;
            width: 50%;
            position: relative;
        }
        input[type="datetime-local"] {
            padding: 8px;
            width: 220px;
            font-size: 1rem;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        .plan-btn {
            margin-top: 10px;
            background-color: #ffb400;
            color: #121212;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .plan-btn:hover {
            background-color: #ffa000;
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
        #selected_movie_display span {
            color: #ffb400;
            font-weight: bold;
        }
        .friend-selection {
            margin-top: 20px;
            text-align: left;
        }
        .friend-selection label {
            color: #ffb400;
        }
        .friend-search {
            margin-bottom: 10px;
            width: 100%;
            padding: 8px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #1c1c1c;
            color: white;
        }
        .friend-suggestions {
            position: absolute;
            width: 100%;
            background-color: #1c1c1c;
            border: 1px solid #ccc;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
            z-index: 10;
            max-height: 150px;
            overflow-y: auto;
            display: none;
            margin-top: 5px;
        }
        .friend-suggestions label {
            display: block;
            padding: 5px;
            color: white;
        }
        .friend-suggestions label:hover {
            background-color: #333;
        }
    </style>
    <script>
        function selectMovie(title) {
            document.getElementById('selected_movie').value = title;
            document.getElementById('selected_movie_title').innerText = title;
            window.scrollTo(0, 0); // film seçilince en üst ekrana kayması için
        }

        function searchFriends() {
            let input = document.getElementById('friend_search');
            let filter = input.value.toUpperCase();
            let suggestions = document.getElementById('friend_suggestions');
            let labels = suggestions.getElementsByTagName('label');

            if (!filter) {
                suggestions.style.display = 'none';
                return;
            }

            suggestions.style.display = 'block';
            Array.from(labels).forEach(label => {
                let friendName = label.innerText || label.textContent;
                if (friendName.toUpperCase().indexOf(filter) > -1) {
                    label.style.display = "";
                } else {
                    label.style.display = "none";
                }
            });
        }

        function selectFriend(friendName) {
            let input = document.getElementById('friend_search');
            input.value = friendName;
            document.getElementById('friend_suggestions').style.display = 'none';
        }
    </script>
</head>
<body>
    <!-- Header ve ana sayfa butonu -->
    <header>
        <h1><span>Plan a Movie Night</span></h1>
        <a href="hosgeldin.php" class="home-btn">Go to Homepage</a>
    </header>

    <!-- Etkinlik planlama formu -->
    <div class="form-container">
        <h3 id="selected_movie_display" style="margin-bottom: 15px;">
            Selected Movie: 
            <span id="selected_movie_title">No movie selected</span>
        </h3>
        <form method="POST" id="eventForm">
            <input type="hidden" name="selected_movie" id="selected_movie">
            <label for="selected_time" style="display: block; font-size: 1rem; margin-bottom: 5px;">Choose Date and Time:</label>
            <input type="datetime-local" name="selected_time" id="selected_time" required>
            
            <div class="friend-selection">
                <h3>Invite Friends</h3>
                <input type="text" id="friend_search" class="friend-search" onkeyup="searchFriends()" placeholder="Search friends...">
                <div id="friend_suggestions" class="friend-suggestions">
                    <?php foreach ($friends as $friend): ?>
                        <label onclick="selectFriend('<?php echo $friend; ?>')">
                            <input type="checkbox" name="friends[]" value="<?php echo $friend; ?>"> 
                            <?php echo $friend; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit" class="plan-btn">Plan Event</button>
        </form>
    </div>

    <p style="margin-top: 20px;">Select a movie by clicking on it!</p>

    <!-- film Listesi -->
    <div class="movie-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="movie-card" onclick="selectMovie('<?php echo htmlspecialchars($row['title']); ?>')">
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
