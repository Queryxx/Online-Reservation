<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id']) && isset($_POST['cancel_reason'])) {
    include 'conn.php';
    $reservation_id = $_POST['reservation_id'];
    $cancel_reason = $_POST['cancel_reason'];
    $user_id = $_SESSION['user_id'];

    // Get reservation details
    $stmt = $conn->prepare("SELECT r.*, u.email AS user_email, u.name AS user_name 
                           FROM reservation_catering r 
                           LEFT JOIN users u ON r.user_id = u.user_id 
                           WHERE r.id = ? AND r.user_id = ?");
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    $stmt->close();

    if ($reservation && strtolower($reservation['status']) === 'pending') {
        // Update reservation status
        $stmt = $conn->prepare("UPDATE reservation_catering SET status = 'Pending Cancellation', cancel_reason = ? WHERE id = ?");
        $stmt->bind_param("si", $cancel_reason, $reservation_id);
        
        if ($stmt->execute()) {
            // Send email notification
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'victoriagrillrestaurant@gmail.com';
                $mail->Password = 'rwno bsje uwrx irqy';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('victoriagrillrestaurant@gmail.com', 'Client');
                $mail->addAddress('victoriagrillrestaurant@gmail.com', 'Admin'); // Admin email
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Cancellation Request - Reservation #' . $reservation_id;
                $mail->Body = "
                    Hello Admin,<br><br>
                    A cancellation request has been submitted:<br><br>
                    <strong>Reservation Details:</strong><br>
                    Customer: {$reservation['user_name']}<br>
                    Date: {$reservation['contract_date']}<br>
                    Time: {$reservation['event_start']}<br>
                    Cancellation Reason: {$cancel_reason}<br><br>
                    Please review this request in your admin dashboard.<br><br>
                ";

                $mail->send();
                $_SESSION['message'] = "Cancellation request submitted successfully.";
            } catch (Exception $e) {
                $_SESSION['message'] = "Request submitted but email notification failed.";
            }
        } else {
            $_SESSION['message'] = "Error submitting cancellation request.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Reservation cannot be cancelled.";
    }

    $conn->close();
    header('Location: rescater.php');
    exit();
} else {
    header('Location: rescater.php');
    exit();
}
?>