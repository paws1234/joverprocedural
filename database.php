<?php


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
     header("Location: safe.php");
    
}

$servername = "localhost";
$db_username = "paws";
$db_password = "paws";
$database = "joverhacking";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";


