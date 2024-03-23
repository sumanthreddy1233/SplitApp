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

// SignUp Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
    echo "SignUp is Successful";
    $stmt->close();
}

// Traditional Login Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = getUserByEmail($email, $conn);
    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        // Set session variables, redirect to user dashboard, etc.
        $_SESSION['user_id'] = $user['id']; // Example session variable
        header('Location: /dashboard.php'); // Redirect to dashboard
    } else {
        // Login failed
        // Handle error, e.g., show a login error message
        echo "Login failed";
    }
}

// OAuth Login Logic
if (isset($_GET['code'])) {
    $authCode = $_GET['code'];

    // Assuming Google's OAuth flow as an example
    $clientId = '672884912392-b5fci078cbiqmtffqen9je41gc8h02cq.apps.googleusercontent.com';
    $clientSecret = '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';
    //have to get my redirect uri here
    $redirectUri = '';
    $tokenUrl = 'https://oauth2.googleapis.com/token';

    $tokenParams = [
        'code' => $authCode,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code'
    ];
    $token = performPostRequest($tokenUrl, $tokenParams);

    if (isset($token['access_token'])) {
        // Use access token to fetch user information
        // Handle user information (create a new user or update existing user)
        // Set session variables, redirect, etc.
    }
}

// Close database connection
$conn->close();
