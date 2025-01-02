<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Movie Recommendation</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #080710;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .background {
            width: 100%;
            height: 100%;
            position: absolute;
        }
        .background .shape {
            height: 300px;
            width: 300px;
            position: absolute;
            border-radius: 50%;
        }
        .shape:first-child {
            background: linear-gradient(#1845ad, #23a2f6);
            left: -100px;
            top: -100px;
        }
        .shape:last-child {
            background: linear-gradient(to right, #ff512f, #f09819);
            right: -100px;
            bottom: -100px;
        }
        .container {
            z-index: 1;
            position: relative;
            width: 400px;
            text-align: center;
            color: #fff;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
            padding: 50px;
            background-color: rgba(255, 255, 255, 0.13);
            border-radius: 10px;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        p {
            font-size: 1rem;
            margin-bottom: 20px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        .button-group a {
            text-decoration: none;
            color: #080710;
            background-color: #ffffff;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
        }
        .button-group a:hover {
            background-color: #23a2f6;
            color: #ffffff;
            box-shadow: 0 0 10px #23a2f6;
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="container">
        <h1>Welcome to the MovieAPP</h1>
        
        <p>Discover and share your favorite movies with friends. Let's start!</p>
        <div class="button-group">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
