<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$step = isset($_SESSION['step']) ? $_SESSION['step'] : 1;

// Check if the 'step' parameter is set in the URL (for navigation between steps)
if (isset($_GET['step'])) {
    $_SESSION['step'] = (int) $_GET['step'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conn.php';

    if (isset($_POST['email']) && $step === 1) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $phone = $_POST['phone'];

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email already exists. Please try a different one.";
        } else {
            if ($password === $confirmPassword) {
                $pin = random_int(100000, 999999);
                $_SESSION['pin'] = $pin;
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;
                $_SESSION['phone'] = $phone;

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'victoriagrillrestaurant@gmail.com';
                    $mail->Password = 'rwno bsje uwrx irqy'; // Replace with app-specific password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS
                    $mail->Port = 587; // TLS port

                    $mail->setFrom('victoriagrillrestaurant@gmail.com', 'Victoria Grill Restaurant');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Registration PIN';
                    $mail->Body = "<p>Your registration PIN is: <strong>$pin</strong></p>";

                    $mail->send();
                    $message = "A PIN has been sent to your email. Enter the PIN to continue registration.";
                    $_SESSION['step'] = 2;

                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } catch (Exception $e) {
                    $message = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $message = "Passwords do not match. Please try again.";
            }
        }
        $stmt->close();
    } elseif (isset($_POST['pin']) && $step === 2) {
        $pin = $_POST['pin'];
        if ($_SESSION['pin'] == $pin) {
            $name = $_SESSION['name'];
            $email = $_SESSION['email'];
            $password = $_SESSION['password'];
            $phone = $_SESSION['phone'];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $phone);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['email'] = $email;
                $_SESSION['step'] = 3;
                $message = "Account successfully created.";

                // JavaScript alert and redirect to index.php
                echo "<script type='text/javascript'>
                        alert('Account successfully created.');
                        window.location = 'logout.php';
                      </script>";
            } else {
                $message = "Error occurred during registration. Please try again.";
            }
            $stmt->close();
        } else {
            $message = "Invalid PIN. Please try again.";
        }
    }
    $conn->close();
} elseif ($step === 3) {
    session_unset(); // Unset session variables
    session_destroy(); // Destroy session
    exit(); // Ensure no further code is executed
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Victoria Grill Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style/menu.css">
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

        .register-container {
            animation: fadeIn 0.6s ease-out;
        }

        .form-input {
            transition: all 0.3s ease-in-out;
        }

        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .submit-button {
            transition: all 0.3s ease-in-out;
        }

        .submit-button:hover {
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
            <!-- Back Arrow -->
            <div class="flex items-center">
                <a href="login.php" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="ml-2">Back</span>
                </a>
            </div>

            <!-- Logo and Title -->
            <div class="flex items-center space-x-4 text-center">
                <img src="victoria.jpg" alt="Victoria Grill Logo" class="h-12 w-12">
                <h1 class="text-2xl font-bold text-gray-800">Victoria Grill Restaurant</h1>
            </div>

            <!-- Placeholder for Right-Side Alignment -->
            <div></div>
        </div>
    </nav>

    <!-- Registration Section -->
    <section class="flex items-center justify-center p-5 min-h-screen p-4">
        <div class="register-container bg-white shadow-md rounded-lg p-8 w-full max-w-md">
            <h2 class="text-3xl font-bold text-gray-800 text-center">Create an Account</h2>
            <p class="text-gray-600 mt-2 text-center">Join us today to make reservations easier!</p>

            <?php if (!empty($message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>
            <!-- Registration Form -->
            <form action="register.php" method="POST" class="mt-8 space-y-6">
                <?php if ($step === 1): ?>
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" required placeholder="Enter your full name"
                            class="form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email"
                            class=" form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number"
                            class="form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>


                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Create a password"
                            class="form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password"
                            class="form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Show Password -->
                    <div class="flex items-center">
                        <input type="checkbox" id="showpassword" name="showpassword"
                            class="h-4 w-4 text-red-500 focus:ring-red-400 border-gray-300 rounded">
                        <label for="showpassword" class="ml-2 text-sm text-gray-600">Show password</label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="submit-button bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg w-full text-lg">Sign Up</button>
                    </div>
                <?php elseif ($step === 2): ?>
                    <!-- PIN -->
                    <div>
                        <label for="pin" class="block text-sm font-medium text-gray-700">Enter PIN</label>
                        <input type="text" id="pin" name="pin" required placeholder="Enter the PIN sent to your email"
                            class="form-input mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="submit-button bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg w-full text-lg">Verify PIN</button>
                    </div>
                <?php endif; ?>
            </form>

            <!-- Login Link -->
            <p class="text-center text-gray-600 mt-6">Already have an account?
                <a href="login.php" class="text-red-500 hover:underline">Login</a>
            </p>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <!-- JavaScript for "Show password" functionality -->
    <script>
        document.getElementById('showpassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const inputType = this.checked ? 'text' : 'password';
            passwordInput.type = inputType;
            confirmPasswordInput.type = inputType;
        });
    </script>
    <script>
        // Check if there's a PHP message and show alert
        <?php if (!empty($message)): ?>
            alert("<?php echo addslashes($message); ?>");
        <?php endif; ?>
    </script>
</body>

</html>