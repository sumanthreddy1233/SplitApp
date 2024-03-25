<?php
// Function to perform an HTTP POST request
function performPostRequest($url, $params) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

// Function to get user by email
function getUserByEmail($email, $conn) {
    if ($stmt = $conn->prepare("SELECT * FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    } else {
        // In a production environment, handle this gracefully.
        die("Error preparing statement: " . $conn->error);
    }
}

// Start a session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '12331233', 'sys');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Traditional Login Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset ($_POST['email']) && isset ($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = getUserByEmail($email, $conn);
    $login_successful = false;
    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        // Set session variables, redirect to user dashboard, etc.
        echo '';
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login_success'] = "Welcome back, " . $user['name'] . "!";// Example session variable
        header('Location: /dashboard.html');
        if (isset ($_SESSION['login_success'])) {
            echo htmlspecialchars($_SESSION['login_success']);
            unset($_SESSION['login_success']); // Clear the message after displaying it
        }
        exit();
    } else {
        // Login failed
        // Handle error, e.g., show a login error message
        echo "Login failed";
        header('Location: /login.html');

    }
}
// Close database connection
$conn->close();
