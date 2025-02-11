<?php
session_start();
require __DIR__ . '/vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$step = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conn.php'; // Include your database connection

    if (isset($_POST['email'])) {
        // Step 1: Send a PIN to the user's email
        $email = $_POST['email'];
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $pin = random_int(100000, 999999); // Generate a 6-digit PIN
            $_SESSION['pin'] = $pin;
            $_SESSION['email'] = $email;

            // Send PIN via email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
                $mail->SMTPAuth = true;
                $mail->Username = 'victoriagrillrestaurant@gmail.com';
                $mail->Password = 'rwno bsje uwrx irqy';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('VictoriaGrill@gmail.com', 'Victoria Grill Restaurant'); // Replace with your email
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your Password Reset PIN';
                $mail->Body = "<p>Your password reset PIN is: <strong>$pin</strong></p>";

                $mail->send();
                $message = "A PIN has been sent to your email. Enter the PIN to reset your password.";
                $step = 2;
            } catch (Exception $e) {
                $message = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Email not found in our records.";
        }
        $stmt->close();
        $conn->close();
    } elseif (isset($_POST['pin'])) {
        // Step 2: Verify the PIN
        $pin = $_POST['pin'];
        if ($_SESSION['pin'] == $pin) {
            $message = "PIN verified. You can now reset your password.";
            $step = 3;
        } else {
            $message = "Invalid PIN. Please try again.";
        }
    } elseif (isset($_POST['new_password'])) {
        // Step 3: Reset the password
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $email = $_SESSION['email'] ?? null;

        if ($email) {
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $newPassword, $email);
            $updateStmt->execute();

            $message = "Your password has been reset successfully.";
            session_destroy();
            $step = 1;
            $updateStmt->close();
            $conn->close();
        } else {
            $message = "Session expired. Please try again.";
            $step = 1;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="overflow-x-hidden font-sans bg-gray-100">
<!-- Header -->
<header class="bg-white text-gray-600 py-4 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center px-4">
        <!-- Back Icon -->
        <a href="login.php" class="text-black hover:text-gray-300">
            <i class="fas fa-arrow-left text-2xl"></i>
        </a>
        <!-- Logo and Title -->
        <div class="flex items-center space-x-4 text-center">
            <img src="victoria.jpg" alt="Victoria Grill Logo" class="h-12 w-12 rounded-full">
            <h1 class="text-2xl font-bold text-gray">Victoria Grill Restaurant</h1>
            </div>
        <div></div>
    </div>
</header>

    <section class="max-w-md mx-auto mt-16 bg-white p-10 mt-5 rounded-lg shadow-lg">
        <?php if ($message): ?>
            <div class="text-center mb-4 text-green-500 font-semibold">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <!-- Step 1: Enter Email -->
            <form method="POST" action="" class="fade-in">
                <h2 class="text-2xl font-bold text-center mb-6 text-gray-700">Forgot Password</h2>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input type="email" name="email" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500" />
                </div>
                <button type="submit" 
                    class="w-full text-white p-3 rounded-md bg-red-600 focus:outline-none focus:ring-2 focus:ring-maroon-500">
                    Send PIN
                </button>
            </form>
        <?php elseif ($step === 2): ?>
            <!-- Step 2: Enter PIN -->
            <form method="POST" action="" class="fade-in">
                <h2 class="text-2xl font-bold text-center mb-6 text-gray-700">Verify PIN</h2>
                <div class="mb-4">
                    <label for="pin" class="block text-sm font-medium text-gray-700">Enter PIN:</label>
                    <input type="text" name="pin" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500" />
                </div>
                <button type="submit"
                    class="w-full text-white p-3 rounded-md bg-red-600 focus:outline-none focus:ring-2 focus:ring-maroon-500">
                    Verify PIN
                </button>
            </form>
        <?php elseif ($step === 3): ?>
            <!-- Step 3: Reset Password -->
            <form method="POST" action="" class="fade-in">
                <h2 class="text-2xl font-bold text-center mb-6 text-gray-700">Reset Password</h2>

                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password:</label>
                    <input type="password" name="new_password" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500" />
                </div>
                <button type="submit"
                    class="w-full text-white p-3 rounded-md bg-red-600 focus:outline-none focus:ring-2 focus:ring-maroon-500">
                    Reset Password
                </button>
            </form>
        <?php endif; ?>
    </section>

    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        .bg-maroon-900 {
            background-color:rgb(229, 47, 47);
        }

        .hover\:bg-maroon-700:hover {
            background-color:rgb(251, 88, 88);
        }

        .hover\:bg-maroon-600:hover {
            background-color:rgb(240, 49, 49);
        }

        .focus\:ring-maroon-500:focus {
            ring-color:rgb(240, 44, 44);
        }

        .text-maroon-700 {
            color:rgb(255, 60, 60);
        }
    </style>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
<?php include 'footer.php'; ?>
</body>

</html>