<?php
//same shits rani sa uban sabotable ra ang functions name ash/lloyd
 session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-4 text-center">Registration</h2>
       
        <form id="registrationForm" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Register</button>
        </form>
        <div id="errorNotification" class="hidden mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline" id="errorMessage"></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20">
                    <title>Close</title>
                    <path fill-rule="evenodd"
                        d="M14.354 5.646a.5.5 0 0 1 0 .708l-8 8a.5.5 0 0 1-.708-.708l8-8a.5.5 0 0 1 .708 0zM5.646 5.646a.5.5 0 0 1 0-.708l8 8a.5.5 0 0 1 .708.708l-8-8a.5.5 0 0 1-.708 0z" />
                </svg>
            </span>
        </div>
        <div id="successModal" class="hidden fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Registration Successful
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" id="modal-description">
                                        You have successfully registered. Redirect to login page in 10 seconds or click the button below.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="redirectButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Redirect Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Already have an account? <a href="index.php"
                    class="font-medium text-indigo-600 hover:text-indigo-500">Login</a></p>
        </div>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function (event) {
            event.preventDefault();

            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                showErrorNotification("Passwords do not match");
                return;
            }

            var formData = new FormData(this);

            fetch('registration_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showErrorNotification(data.error); 
                } else if (data.message) {
                    showSuccessModal(); 
                    setTimeout(function() {
                        window.location.href = 'index.php'; 
                    }, 10000); 
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
            setTimeout(function() {
                errorNotification.classList.add('hidden');
                errorNotification.classList.remove('block');
            }, 5000);
        }

        function showSuccessModal() {
            var successModal = document.getElementById('successModal');
            successModal.classList.remove('hidden');

            var redirectButton = document.getElementById('redirectButton');
            redirectButton.addEventListener('click', function() {
                window.location.href = 'index.php'; 
            });
        }
    </script>
</body>

</html>
