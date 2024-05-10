<?php
//so kani mao ni ang dashboard ang gamit sa pag first if kay nag check sa session kung naa ang username og ang csrf token wa nako na butang pero naa sa hosted na files
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}
//kani chuychuy generate rag random shit as a payload para sa atong authentication sa session para logout ni ha ash/lloyd
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

// ako gi sud ang generated token as a hidden shit para masud as a payload sa request inig logout parehas rani sa uban parts ani na files //
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
        <p class="text-gray-700">You Hacked our login motherfucker.</p>
        
        <form action="logout.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <button type="submit" class="mt-4 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Logout</button>
        </form>
    </div>
</body>

</html>

