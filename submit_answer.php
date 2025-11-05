<?php
// submit_answer.php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// --- A. Security Check ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit();
}

// --- B. Handle Next Question Request (GET) ---
if (isset($_GET['action']) && $_GET['action'] === 'next') {
    // Increment the question index and redirect to quiz.php for the new question
    $_SESSION['current_question_index']++;
    $_SESSION['awaiting_next'] = false; // Reset flag
    
    // This is a browser redirect, not a JSON response
    header("Location: quiz.php");
    exit();
}

// --- C. Handle Answer Submission (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['question_id'], $_POST['answer'])) {
    $questionId = (int)$_POST['question_id'];
    $submittedAnswer = htmlspecialchars($_POST['answer']);
    
    $response = ['status' => 'error', 'message' => 'Processing failed.'];

    // Prevent submitting the same question multiple times
    if ($_SESSION['awaiting_next'] === true) {
        $response['message'] = 'Answer already submitted for this question.';
        http_response_code(409); // Conflict
        echo json_encode($response);
        exit();
    }

    // Fetch the correct answer from the database
    $stmt = $conn->prepare("SELECT correct_option FROM questions WHERE id=?");
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $correctOption = $row['correct_option'];

        $isCorrect = ($submittedAnswer === $correctOption);

        if ($isCorrect) {
            $_SESSION['score']++;
            $feedback = "Correct! 🎉";
        } else {
            $feedback = "Wrong! ❌ The correct answer was option **'".strtoupper($correctOption)."'**";
        }
        
        $_SESSION['awaiting_next'] = true; // Set flag to prevent resubmission

        $response = [
            'status' => 'success',
            'feedback' => $feedback,
            'score' => $_SESSION['score'],
            'correct_answer' => $correctOption
        ];

    } else {
        $response['message'] = "Question ID not found.";
    }

    $stmt->close();
    $conn->close();
    echo json_encode($response);
    exit();
}

// Default response if neither GET nor POST conditions met
http_response_code(400); // Bad Request
echo json_encode(['status' => 'error', 'message' => 'Invalid request method or parameters.']);
exit();
?>