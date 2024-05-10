<?php
//para sure na post request rajud ang maka gamit sa kani na endpoint sa backend
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: safe.php");
    exit;
}
//headers rani para security ash/lloyd
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
session_start();
session_regenerate_id();

$response = array();
//gi check ang payload sa csrf token kung na generate ba sa register na page chuy chuy rani
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $response['error'] = "Oops! Something went wrong. Please try again later.";
    echo json_encode($response);
    exit;
}
//gi filter ang input shits para di ma sql injection og xss shits
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
//sabotable ra ang logic ubos sa pag register rani basic shits
if (strlen($username) > 10) {
    $response['error'] = "Username should be maximum of 10 characters.";
    echo json_encode($response);
    exit;
}

if (strlen($password) > 20) {
    $response['error'] = "Password should be maximum of 20 characters.";
    echo json_encode($response);
    exit;
}

if (empty($username) || empty($password)) {
    $response['error'] = "Please enter both username and password.";
    echo json_encode($response);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$servername = "localhost";
$db_username = "paws";
$db_password = "paws";
$database = "joverhacking";
$conn = mysqli_connect($servername, $db_username, $db_password, $database);

if (!$conn) {
    $response['error'] = "Connection failed: " . mysqli_connect_error();
    echo json_encode($response);
    exit;
}

$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    $response['error'] = "SQL statement failed.";
    echo json_encode($response);
    exit;
}

mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    $response['message'] = "Registration successful.";
    echo json_encode($response);
} else {
    $response['error'] = "Error: " . mysqli_error($conn);
    echo json_encode($response);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
