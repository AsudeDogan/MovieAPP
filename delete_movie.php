<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Film ID'sini al
if (isset($_GET['id'])) {
    $movie_id = $_GET['id'];

    // Eğer film ID'si var ise, film bilgilerini veritabanından sil
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Silme işlemi
        $delete_sql = "DELETE FROM movies WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $movie_id);

        if ($delete_stmt->execute()) {
            echo "Movie deleted successfully!";
            header("Location: admin_page.php");
            exit();
        } else {
            echo "Error deleting movie!";
        }

        $delete_stmt->close();
    }
} else {
    echo "Invalid movie ID!";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Movie</title>
    <script type="text/javascript">
        function openModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: #1f1f1f;
            border-radius: 10px;
        }
        .button {
            width: 100%;
            padding: 12px;
            background-color: #e74c3c;
            color: white;
            border: none;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #c0392b;
        }
        .go-back-btn {
            background-color:rgb(158, 150, 150); 
            margin-top: 10px;
        }
        .go-back-btn:hover {
            background-color:rgb(180, 173, 173);
        }
        
        /* Modal Container */
        .modal {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        /* Modal Content */
        .modal-content {
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .modal-button {
            width: 45%;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            margin: 10px;
            cursor: pointer;
        }

        .modal-button.delete {
            background-color: #e74c3c;
            color: white;
        }

        .modal-button.delete:hover {
            background-color: #c0392b;
        }

        .modal-button.cancel {
            background-color: #7f8c8d;
            color: white;
        }

        .modal-button.cancel:hover {
            background-color: #95a5a6;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Are you sure you want to delete this movie?</h2>
    <button onclick="openModal()" class="button">Delete Movie</button>
    <button onclick="window.location.href='admin_page.php'" class="go-back-btn">Cancel</button>
</div>

<!-- check again -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to delete this movie?</p>
        <form method="POST" action="">
            <button type="submit" class="modal-button delete">Delete Movie</button>
        </form>
        <button class="modal-button cancel" onclick="closeModal()">Cancel</button>
    </div>
</div>

</body>
</html>
