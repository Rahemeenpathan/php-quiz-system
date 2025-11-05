<?php
// user_registration.php
session_start();
// The include file name has been corrected to db_connect.php
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        $message = "Error: Email already registered!";
    } else {
        // Hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Use a prepared statement to prevent SQL Injection
        // Note: Assuming 'role' defaults to 'user' in your database table
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $message = "Registration successful! You can now <a href='user_login.php'>login</a>.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $stmt_check->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Quiz System</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
        }

        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-bottom: 2px solid #2575fc;
            outline: none;
            font-size: 15px;
        }

        .register-container button {
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

        .register-container button:hover {
            background: #6a11cb;
        }

        .message {
            color: #333;
            margin-top: 10px;
        }

        .login-link {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-link a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>📝 Register</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Enter your name" required><br>
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <button type="submit">Register</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <p class="login-link">Already have an account? <a href="user_login.php">Login here</a></p>
    </div>
</body>
</html>