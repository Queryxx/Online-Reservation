<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (!empty($username) && !empty($password)) {
        // Check if the email/username exists in the database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, verify the password
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Start the session and set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];

                // Redirect to account page
                header("Location: userdashboard.php");
                exit();
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            $error_message = "User not found. Please check your email.";
        }

        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Victoria Grill Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            animation: fadeIn 0.6s ease-out;
        }

        .form-input {
            transition: all 0.3s ease-in-out;
        }

        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .login-button {
            transition: all 0.3s ease-in-out;
        }

        .login-button:hover {
            transform: translateY(-2px);
        }

        .nav-link {
            transition: all 0.2s ease-in-out;
        }

        .nav-link:hover {
            transform: translateX(5px);
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <a href="index.php" class="nav-link text-gray-600 hover:text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="ml-2">Back</span>
                </a>
            </div>
            <div class="flex items-center space-x-4 text-center">
                <img src="victoria.jpg" alt="Victoria Grill Logo" class="h-12 w-12">
                <h1 class="text-2xl font-bold text-gray-800">Victoria Grill Restaurant</h1>
            </div>
            <div></div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="flex items-center justify-center p-5 min-h-screen">
        <div class="login-container bg-white shadow-md rounded-lg p-8 w-full max-w-md">
            <h2 class="text-3xl font-bold text-gray-800 text-center">Welcome</h2>
            <p class="text-gray-600 mt-2 text-center">Please login to continue.</p>

            <!-- Display Error Message -->
            <?php if (!empty($error_message)) : ?>
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="" method="POST" class="mt-8 space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="text" id="email" name="email" required placeholder="Enter your email"
                        class="form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password"
                        class="form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="showpassword" name="showpassword"
                            class="h-4 w-4 text-red-500 focus:ring-red-400 border-gray-300 rounded">
                        <label for="showpassword" class="ml-2 text-sm text-gray-600">Show password</label>
                    </div>
                    <a href="forgot-password.php" class="text-sm text-red-500 hover:underline">Forgot Password?</a>
                </div>
                <div>
                    <button type="submit"
                        class="login-button bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg w-full text-lg">Login</button>
                </div>
            </form>
            <p class="text-center text-gray-600 mt-6">Don’t have an account?
                <a href="register.php" class="text-red-500 hover:underline">Sign up</a>
            </p>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        document.getElementById('showpassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('password');
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>

</html>