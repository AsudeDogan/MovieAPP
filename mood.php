<?php
session_start();

// Veritabanı bağlantısı
$conn = new mysqli('localhost', 'root', '', 'movie_recommendation');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Oturum sıfırlama işlemi
if (isset($_POST['reset_home'])) {
    $_SESSION['mood_step'] = 1;
    $_SESSION['answers'] = [];
    header("Location: hosgeldin.php");
    exit();
}

// Oturumda mood adımları yoksa başlat
if (!isset($_SESSION['mood_step'])) {
    $_SESSION['mood_step'] = 1;
    $_SESSION['answers'] = [];
}

// Kullanıcı bir cevap verdiyse kaydet ve adım ilerlet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['answer'])) {
    $_SESSION['answers'][] = $_POST['answer'];
    $_SESSION['mood_step']++;
}

// Kullanıcı "Try Again" e basarsa oturum sıfırla
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])) {
    $_SESSION['mood_step'] = 1;
    $_SESSION['answers'] = [];
}

// Film önerisini tutacak değişken
$recommendation = null;

if ($_SESSION['mood_step'] > 2) {
    $mood = $_SESSION['answers'][0];
    $subMood = $_SESSION['answers'][1];

    $query = "SELECT title, image_url FROM movies WHERE mood = ? AND sub_mood = ? ORDER BY RAND() LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $mood, $subMood);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $recommendation = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How You Feelin'?</title>
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
        .question {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .options button {
            background-color: #ffb400;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }
        .options button:hover {
            background-color: #ffa000;
        }
        .recommendations {
            text-align: center;
            margin-top: 20px;
        }
        .recommendations img {
            width: 200px;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .button-container {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .reset-btn, .home-btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .reset-btn {
            background-color: #ffb400;
            color: white;
            border: none;
        }
        .home-btn {
            background-color: #444;
            color: white;
            border: none;
        }
        .reset-btn:hover {
            background-color: #ffa000;
        }
        .home-btn:hover {
            background-color: #666;
        }
    </style>
</head>
<body>
    <?php if ($_SESSION['mood_step'] === 1): ?>
        <div class="question">How you feelin' today?</div>
        <form method="POST" class="options">
            <button type="submit" name="answer" value="Happy">I feel Happy</button>
            <button type="submit" name="answer" value="Sad">I feel Sad</button>
        </form>
    <?php elseif ($_SESSION['mood_step'] === 2): ?>
        <div class="question">
            <?php if ($_SESSION['answers'][0] === "Happy"): ?>
                Good to hear that! Select the genre you're interested in the options below:
            <?php elseif ($_SESSION['answers'][0] === "Sad"): ?>
                Hope things get better. Would you like to watch a depressive or comforting movie?
            <?php endif; ?>
        </div>
        <form method="POST" class="options">
            <?php if ($_SESSION['answers'][0] === "Happy"): ?>
                <button type="submit" name="answer" value="Adventure">Adventure</button>
                <button type="submit" name="answer" value="Comedy">Comedy</button>
                <button type="submit" name="answer" value="Romance">Romance</button>
            <?php elseif ($_SESSION['answers'][0] === "Sad"): ?>
                <button type="submit" name="answer" value="Depressive">Depressive</button>
                <button type="submit" name="answer" value="Comforting">Comforting</button>
            <?php endif; ?>
        </form>
    <?php elseif ($_SESSION['mood_step'] === 3 && $recommendation): ?>
        <div class="recommendations">
            <img src="<?php echo htmlspecialchars($recommendation['image_url']); ?>" alt="<?php echo htmlspecialchars($recommendation['title']); ?> Poster">
            <h2><?php echo htmlspecialchars($recommendation['title']); ?></h2>
        </div>
        <div class="button-container">
            <form method="POST">
                <button type="submit" name="reset" class="reset-btn">Try Again</button>
            </form>
            <form method="POST">
                <button type="submit" name="reset_home" class="home-btn">Go to Homepage</button>
            </form>
        </div>
    <?php else: ?>
        <p>No recommendations available. Please try again.</p>
    <?php endif; ?>
</body>
</html>
