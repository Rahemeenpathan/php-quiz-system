<?php
// results.php
session_start();

// Redirect if no quiz session
if (!isset($_SESSION['name']) || !isset($_SESSION['score']) || !isset($_SESSION['question_ids'])) {
    header("Location: user_login.php");
    exit();
}

$user = htmlspecialchars($_SESSION['name']);
$score = $_SESSION['score'];
$total = count($_SESSION['question_ids']);

// Generate appreciation message based on score
$percentage = ($total > 0) ? ($score / $total) * 100 : 0;
if ($percentage >= 90) {
    $msg = "🌟 Excellent! You are a PHP master!";
} elseif ($percentage >= 70) {
    $msg = "👍 Very good! Keep practicing!";
} elseif ($percentage >= 50) {
    $msg = "😊 Good! But you can improve!";
} else {
    $msg = "💡 Keep learning! Don’t give up!";
}

// Reset session variables related to the quiz for a new attempt
unset($_SESSION['question_ids']);
unset($_SESSION['current_question_index']);
unset($_SESSION['score']);
unset($_SESSION['awaiting_next']); // Clean up the new flag

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz Results</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="result-container">
    <h1>Quiz Completed!</h1>
    <p>Hello, <strong><?php echo $user; ?></strong>!</p>
    <p class="score">Your Score: <?php echo $score; ?> / <?php echo $total; ?></p>
    <p class="message"><?php echo $msg; ?></p>
    <a href="index.php"><button>Play Again</button></a>
</div>
</body>
</html>