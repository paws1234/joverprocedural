<?php
session_start(); 

// mao niy mo generate og csrf token as a payload shit apra authentication chuy chuy rani
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>
//login shit ni diri sabotable ra ang fucntion names nya kabaw man ka ash unsa nanng mga requests shits so sabotable rana//
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>
       
        <form id="loginForm" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username" maxlength="10"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" maxlength="20"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Login</button>
        </form>
        <div id="errorNotification"
            class="hidden mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline" id="errorMessage"></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20">
                    <title>Close</title>
                    <path fill-rule="evenodd"
                        d="M14.354 5.646a.5.5 0 0 1 0 .708l-8 8a.5.5 0 0 1-.708-.708l8-8a.5.5 0 0 1 .708 0zM5.646 5.646a.5.5 0 0 1 0-.708l8 8">
                </svg>
            </span>
        </div>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Don't have an account? <a href="registration.php"
                    class="font-medium text-indigo-600 hover:text-indigo-500">Register</a></p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            event.preventDefault();

            var formData = new FormData(this);

            fetch('process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showErrorNotification(data.error);
                    } else if (data.success) {
                        sessionStorage.setItem('username', data.username);
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        function showErrorNotification(errorMessage) {
            var errorNotification = document.getElementById('errorNotification');
            var errorMessageElement = document.getElementById('errorMessage');

            errorMessageElement.textContent = errorMessage;
            errorNotification.classList.remove('hidden');
            errorNotification.classList.add('block');
            setTimeout(function () {
                errorNotification.classList.add('hidden');
                errorNotification.classList.remove('block');
            }, 5000);
        }
    </script>

</body>

</html>
