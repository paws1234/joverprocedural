<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: safe.php");
    exit;
}

session_start();

if (!isset($_SESSION['last_ip'])) {
    $_SESSION['last_ip'] = ''; 
}

header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

$response = array();

if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $response['error'] = "CSRF token validation failed.";
    echo json_encode($response);
    exit;
}

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if (empty($username) || empty($password)) {
    $response['error'] = "Please enter both username and password.";
    echo json_encode($response);
    exit;
}

$servername = "localhost";
$db_username = "paws";
$db_password = "paws";
$database = "joverhacking";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    $response['error'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

$max_attempts_per_minute = 10;
$attempt_window_seconds = 60;
$min_ip_change_time = 600; 

$ip_address = $_SERVER['REMOTE_ADDR'];


$last_attempt_time = time() - $min_ip_change_time;
$sql = "SELECT COUNT(*) AS num_attempts FROM login_attempts WHERE ip_address=? AND timestamp >= ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['error'] = "SQL statement failed: " . $conn->error;
    echo json_encode($response);
    exit;
}
$stmt->bind_param("si", $ip_address, $last_attempt_time);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$num_attempts = $row['num_attempts'];
$stmt->close();

if ($num_attempts >= $max_attempts_per_minute) {
    $response['error'] = "Login failed Please try again later.";
    echo json_encode($response);
    exit;
}


$sql = "SELECT ip_address, timestamp FROM login_attempts WHERE username=? ORDER BY timestamp DESC LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['error'] = "SQL statement failed: " . $conn->error;
    echo json_encode($response);
    exit;
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row) {
    $last_ip_address = $row['ip_address'];
    $last_login_time = strtotime($row['timestamp']);
    if (time() - $last_login_time < $min_ip_change_time && $last_ip_address !== $ip_address) {
        $response['error'] = "Login failed";
        echo json_encode($response);
        exit;
    }
}

$sql = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['error'] = "SQL statement failed: " . $conn->error;
    echo json_encode($response);
    exit;
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    $response['error'] = "Invalid username or password. Please try again.";
    echo json_encode($response);
    $conn->close();
    exit;
}

$userData = $result->fetch_assoc();
$hashed_password = $userData['password'];

if (!password_verify($password, $hashed_password)) {
    $response['error'] = "Invalid username or password. Please try again.";
    echo json_encode($response);
    $conn->close();
    exit;
}

$_SESSION['username'] = $username;

$sql = "INSERT INTO login_attempts (username, ip_address, timestamp) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['error'] = "SQL statement failed: " . $conn->error;
    echo json_encode($response);
    exit;
}
$stmt->bind_param("ss", $username, $ip_address);
$stmt->execute();
$stmt->close();
$conn->close();

$response['success'] = true;
$response['redirect'] = 'dashboard.php';
echo json_encode($response);
exit;
