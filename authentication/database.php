<?php


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
     header("Location: safe.php");
    
}

$servername = "	sql109.infinityfree.com";
$username = "if0_36455033";
$password = "2gGRF5hehcCz";
$database = "if0_36455033_joverhacking";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

