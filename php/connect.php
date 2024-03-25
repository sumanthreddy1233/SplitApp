<?php
// Function to perform an HTTP POST request
function performPostRequest($url, $params)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

// Function to get user by email
function getUserByEmail($email, $conn)
{
    if ($stmt = $conn->prepare("SELECT * FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    } else {
        // In a production environment, handle this gracefully.
        die ("Error preparing statement: " . $conn->error);
    }
}

// Start a session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '12331233', 'sys');
if ($conn->connect_error) {
    die ('Connection failed: ' . $conn->connect_error);
}

// SignUp Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset ($_POST['name'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // Check if the email already exists
    $existingUser = getUserByEmail($email, $conn);
    if ($existingUser) {
        echo "Email already exists";
        header('Location: /index.html?message=Email already exists');
    exit();
    } else {
        $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        // ... existing signup logic
        $stmt->execute();
        $_SESSION['signup_success'] = "SignUp is Successful";
        $stmt->close();
        echo "SignUp is Successful";
        header('Location: /login.html?message=SignUp is Successful');
        exit();

    }
}

// Close database connection
$conn->close();
