<?php
// user_login.php (was user_logic.php)
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'] ?? 'user'; // Ensure role is set
            
            // Redirect to the main index page
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with that email address!";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Quiz System</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-bottom: 2px solid #2575fc;
            outline: none;
            font-size: 15px;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #2575fc;
            border: none;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-container button:hover {
            background: #6a11cb;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .register-link {
            margin-top: 15px;
            font-size: 14px;
        }

        .register-link a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Login</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <button type="submit">Login</button>
        </form>
        <p class="error"><?php echo $error; ?></p>
        <p class="register-link">Don’t have an account? <a href="user_registration.php">Register here</a></p>
    </div>
</body>
</html>