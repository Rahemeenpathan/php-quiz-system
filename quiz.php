<?php
// quiz.php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'db_connect.php';

// --- A. Security Check ---
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

// --- B. Name Check and Prompt ---
// If name is NOT set (or is empty after being pulled from the database)
if (empty($_SESSION['name'])) {
    if (isset($_POST['player_name']) && trim($_POST['player_name']) !== '') {
        // 1. If form submitted, set the name and refresh
        $_SESSION['name'] = htmlspecialchars(trim($_POST['player_name']));
        header("Location: quiz.php");
        exit();
    } else {
        // 2. If name is missing, show the name input form
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enter Your Name</title>
<link rel="stylesheet" href="style.css">
<style>
    /* Styling block for the name input */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .name-container { 
        background: rgba(255, 255, 255, 0.2); 
        padding: 50px; 
        border-radius: 15px; 
        width: 300px; 
        text-align: center; 
        color: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }
    .name-container h2 {
        font-size: 2rem;
        margin-bottom: 20px;
    }
    .name-container input[type="text"] {
        padding: 12px; 
        margin-bottom: 25px; 
        border-radius: 8px; 
        border: 2px solid #ff6a00; 
        width: 90%; 
        box-sizing: border-box;
        font-size: 1rem;
    }
    .name-container button { 
        width: 100%; 
        padding: 12px;
        background: #ff6a00;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .name-container button:hover {
        background: #ff8c42;
        transform: scale(1.05);
    }
</style>
</head>
<body>
    <div class="name-container">
        <h2>👋 What's your name?</h2>
        <form method="POST" action="quiz.php">
            <input type="text" name="player_name" placeholder="Enter Display Name" required>
            <button type="submit">Start Quiz</button>
        </form>
        <a href="logout.php"><button class="logout-btn" style="background-color: #dc3545; width: 100%; margin-top: 15px;">Logout</button></a>
    </div>
</body>
</html>
<?php
        exit(); // Stop execution here until name is entered
    }
}
// -----------------------------------------------------------------


// --- C. Initial Quiz Setup ---
if (!isset($_SESSION['question_ids'])) {
    // Fetch 20 random question IDs
    $sql = "SELECT id FROM questions ORDER BY RAND() LIMIT 20";
    $result = $conn->query($sql);
    $questionIds = [];
    while ($row = $result->fetch_assoc()) {
        $questionIds[] = $row['id'];
    }
    $_SESSION['question_ids'] = $questionIds;
    $_SESSION['current_question_index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['awaiting_next'] = false;
}

$current_index = $_SESSION['current_question_index'];
$total_questions = count($_SESSION['question_ids']);

// --- D. Check for Quiz Completion ---
if ($current_index >= $total_questions) {
    header("Location: results.php");
    exit();
}

// --- E. Fetch Current Question ---
$currentQuestionId = $_SESSION['question_ids'][$current_index];
$stmt = $conn->prepare("SELECT * FROM questions WHERE id=?");
$stmt->bind_param("i", $currentQuestionId);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP Quiz</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="quiz-container">
    <h2>Question <span id="current-q-num"><?php echo $current_index + 1; ?></span> / <?php echo $total_questions; ?></h2>
    <p>Player: <span class="score-badge"><?php echo htmlspecialchars($_SESSION['name']); ?></span>! 
    Score: <span class="score-badge" id="current-score"><?php echo $_SESSION['score']; ?></span></p>
    
    <p class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></p>

    <div id="options-container">
        <?php
        $options = [
            'a' => $question['option_a'],
            'b' => $question['option_b'],
            'c' => $question['option_c'],
            'd' => $question['option_d']
        ];
        foreach ($options as $key => $option):
            if ($option != ''): ?>
            <button type="button" class="option-btn" data-key="<?php echo $key; ?>" data-id="<?php echo $currentQuestionId; ?>">
                <?php echo htmlspecialchars($option); ?>
            </button>
        <?php endif; endforeach; ?>
    </div>
    
    <div class="feedback" id="feedback"></div>

    <button id="nextBtn" class="next-btn" style="display:none;">Next Question</button>

    <a href="logout.php"><button class="logout-btn">Logout</button></a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const optionsContainer = document.getElementById('options-container');
    const nextBtn = document.getElementById('nextBtn');
    const feedbackDiv = document.getElementById('feedback');
    const currentScoreSpan = document.getElementById('current-score');
    
    // Check if the user is already awaiting the next question (from a previous answer)
    // This is useful if the user refreshed the page after answering but before clicking next.
    if (<?php echo empty($_SESSION['awaiting_next']) ? 'false' : 'true'; ?>) {
        // We can't re-apply styling here, but we can ensure the next button is visible
        optionsContainer.querySelectorAll('.option-btn').forEach(btn => btn.disabled = true);
        feedbackDiv.innerHTML = "Click **Next Question** to continue.";
        feedbackDiv.classList.add('show');
        nextBtn.style.display = 'block';
    }


    // --- 1. Handle Option Click (AJAX Submission) ---
    optionsContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('.option-btn');
        if (!btn || btn.disabled) return;

        const selectedKey = btn.dataset.key;
        const questionId = btn.dataset.id;
        
        // Disable all buttons to prevent double-clicking
        optionsContainer.querySelectorAll('.option-btn').forEach(b => b.disabled = true);

        // Send answer to the server using AJAX
        fetch('submit_answer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `question_id=${questionId}&answer=${selectedKey}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Apply visual feedback based on the server's response
                const correctAnswerKey = data.correct_answer;
                
                optionsContainer.querySelectorAll('.option-btn').forEach(b => {
                    if (b.dataset.key === correctAnswerKey) {
                        b.classList.add('correct-anim');
                        b.style.backgroundColor = '#28a745';
                    } else if (b.dataset.key === selectedKey) {
                        b.classList.add('wrong-anim');
                        b.style.backgroundColor = '#dc3545';
                    } else {
                        b.style.opacity = '0.7';
                    }
                });
                
                // Update score and display feedback
                currentScoreSpan.textContent = data.score;
                feedbackDiv.innerHTML = data.feedback;
                feedbackDiv.classList.add('show');
                nextBtn.style.display = 'block';

            } else {
                feedbackDiv.innerHTML = `Error: ${data.message || 'Something went wrong.'}`;
                nextBtn.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            feedbackDiv.innerHTML = 'Network error. Please try again.';
            nextBtn.style.display = 'block';
        });
    });

    // --- 2. Handle Next Button Click ---
    nextBtn.addEventListener('click', () => {
        // Redirect the user, the server will handle incrementing the question index
        window.location.href = 'submit_answer.php?action=next';
    });
});
</script>
</body>
</html>