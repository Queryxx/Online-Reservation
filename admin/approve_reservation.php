<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'conn.php'; // Include the database connection
require '../vendor/autoload.php'; // Include PHPMailer autoload file

if (isset($_GET['id'])) {
    $reservation_id = $_GET['id'];

    // Update the reservation status to 'Approved'
    $query = "UPDATE reservation SET status = 'Confirmed' WHERE reservation_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $reservation_id);
    if (!$stmt->execute()) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }
    $stmt->close();

    // Fetch the user's email to send the notification
    $query_user = "SELECT u.email, u.name FROM reservation r 
                   JOIN users u ON r.user_id = u.user_id
                   WHERE r.reservation_id = ?";
    $stmt_user = $conn->prepare($query_user);
    if ($stmt_user === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt_user->bind_param("i", $reservation_id);
    if (!$stmt_user->execute()) {
        die('Execute failed: ' . htmlspecialchars($stmt_user->error));
    }
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();
    $stmt_user->close();

    if ($user) {
        $user_email = $user['email'];
        $user_name = $user['name'];

        // Send email notification to the user
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';   
            $mail->SMTPAuth = true;
            $mail->Username = 'victoriagrillrestaurant@gmail.com';
            $mail->Password = 'rwno bsje uwrx irqy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('victoriagrillrestaurant@gmail.com', 'Victoria Grill Restaurant');
            $mail->addAddress($user_email, $user_name); // Add a recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Reservation Approved';
            $mail->Body = "
                <h1>Hello $user_name,</h1>
                <p>Your reservation with ID <strong>$reservation_id</strong> has been approved.</p>
                <p>Thank you for choosing Victoria Grill Restaurant. We look forward to serving you.</p>
                <p>Best Regards,<br>Victoria Grill Restaurant</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "User not found.";
    }

    // Redirect back to the reservations page
    header("Location: manage_reservation.php");
    exit();
}
?>