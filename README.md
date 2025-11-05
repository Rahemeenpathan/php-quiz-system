PHP/MySQL Quiz System🌟 Project OverviewA dynamic PHP/MySQL quiz system featuring secure user authentication, real-time AJAX scoring, and robust session management.This is a multi-page web application designed to deliver an interactive and responsive quiz experience. It includes user registration and login, random question selection, and immediate visual feedback on answers without full page reloads.🛠️ Technical StackComponentTechnologyRoleServer-Side LogicPHPHandles all routing, session management, and server-side processing for quiz flow.DatabaseMySQLStores user credentials, hashed passwords, and quiz questions/answers.InteractivityJavaScript (AJAX)Manages asynchronous communication (submitting answers to submit_answer.php) for instant feedback and score updates.StylingCustom CSSProvides the gradient backgrounds, animations, and responsive layout.SecurityPHP password_hash() & password_verify()Used for secure storage and validation of user passwords.⚙️ Setup and Installation GuideFollow these steps to get a local copy of the project running on your machine.PrerequisitesLocal Server Environment: A running server stack (e.g., XAMPP, WAMP, or Laragon) that includes Apache and MySQL.PHP Version: PHP 7.4 or higher.Step 1: Prepare Project FilesDownload all project files (index.php, quiz.php, db_connect.php, etc.) into a single folder (e.g., php-quiz-system) inside your server's document root (e.g., htdocs or www).Ensure this README.md file is also in the root of that folder.Step 2: Configure Database ConnectionOpen the db_connect.php file.Verify or update your database connection details to match your local setup:$servername = "localhost";
$username = "root";
$password = "rahemeen23"; // CHANGE THIS if your MySQL root password is different!
$dbname = "quiz_db_final";
$port = 3308; // Check if your MySQL runs on this exact port (default is often 3306)
Step 3: Create Database and TablesOpen your MySQL management tool (e.g., phpMyAdmin, Adminer).Create a new database named quiz_db_final.Execute the following SQL commands to create the required tables:-- Users Table (for registration and login)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questions Table (to store the quiz content)
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('a', 'b', 'c', 'd') NOT NULL
);
Insert Sample Data: You must manually insert at least 20 questions into the questions table to ensure the quiz runs correctly (LIMIT 20 is hardcoded in quiz.php).Step 4: Run the ApplicationOpen your web browser.Navigate to the project folder URL (e.g., http://localhost/php-quiz-system/).Register a new user via the link on the welcome page.Log in and start the quiz!