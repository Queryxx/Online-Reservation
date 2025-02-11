
<?php
// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Ensure this path is correct

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Generate a random PIN
$pin = rand(100000, 999999);
$_SESSION['pin'] = $pin;

// Fetch the logged-in user's email from the session
$userEmail = $_SESSION['user_email'];

// Configure PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'victoriagrillrestaurant@gmail.com';
    $mail->Password = 'rwno bsje uwrx irqy';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('victoriagrillrestaurant@gmail.com', 'Victoria Grill Restaurant');
    $mail->addAddress($userEmail); // Send to the logged-in user's email

    $mail->isHTML(true);
    $mail->Subject = 'Your PIN Code';
    $mail->Body    = "Your PIN code is $pin.";

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
?>
