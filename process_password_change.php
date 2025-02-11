<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'conn.php'; // Include database connection

session_start();

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_pin'])) {
        // Get user's email from session
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'User not logged in';
            echo json_encode($response);
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Get user's email from database
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $email = $row['email'];
            $pin = rand(100000, 999999);
            $_SESSION['password_change_pin'] = $pin;
            $_SESSION['pin_timestamp'] = time(); // Add timestamp for expiry

            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'victoriagrillrestaurant@gmail.com';
                $mail->Password = 'rwno bsje uwrx irqy';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('victoriagrillrestaurant@gmail.com', 'Victoria Grill Restaurant');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Change Verification PIN';
                $mail->Body = "Your verification PIN for password change is <b>$pin</b><br>This PIN will expire in 10 minutes.";

                $mail->send();
                $response['success'] = true;
                $response['message'] = 'PIN has been sent to your email.';
            } catch (Exception $e) {
                $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $response['message'] = 'User email not found';
        }
    } elseif (isset($_POST['verify_pin'])) {
        // Verify PIN and change password
        $submittedPin = $_POST['verification_pin'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Check if PIN is valid and not expired (10 minutes)
        if (!isset($_SESSION['password_change_pin']) || 
            $_SESSION['password_change_pin'] != $submittedPin ||
            (time() - $_SESSION['pin_timestamp']) > 600) {
            $response['message'] = 'Invalid or expired PIN';
        } elseif ($newPassword !== $confirmPassword) {
            $response['message'] = 'Passwords do not match';
        } else {
            // Update password in database
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $userId = $_SESSION['user_id'];
            
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Password successfully changed';
                // Clear the PIN from session
                unset($_SESSION['password_change_pin']);
                unset($_SESSION['pin_timestamp']);
            } else {
                $response['message'] = 'Error updating password';
            }
        }
    }
}

echo json_encode($response);
?>