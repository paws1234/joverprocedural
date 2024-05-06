<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: safe.php");
    exit;
}
session_start();

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    exit;
}

$_SESSION = array();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
header("Location: index.php");
exit;
