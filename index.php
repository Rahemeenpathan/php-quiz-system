<?php
session_start();

// Redirect to quiz if user is logged in
if (isset($_SESSION['user_id'])) {
    header("Location: quiz.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to the Quiz</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        color: white;
    }

    .card {
        background: rgba(255, 255, 255, 0.2);
        padding: 50px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .card h1 {
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    .card p {
        font-size: 1.2rem;
        margin-bottom: 30px;
    }

    .login-btn {
        display: inline-block;
        padding: 12px 25px;
        background: #ff6a00;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: bold;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .login-btn:hover {
        background: #ff8c42;
        transform: scale(1.05);
    }
</style>
</head>
<body>
    <div class="card">
        <h1>Welcome to the PHP Quiz!</h1>
        <p>Please log in to start playing.</p>
        <a href="user_login.php" class="login-btn">Log In</a>
    </div>
</body>
</html>