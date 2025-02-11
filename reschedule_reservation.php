<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];

    // Validate inputs
    if (empty($reservation_id) || empty($new_date) || empty($new_time)) {
        echo "<script>alert('All fields are required.'); window.location.href='orders.php';</script>";
        exit;
    }

    // Check if the new date and time are in the future
    $currentDate = new DateTime();
    $selectedDate = new DateTime("$new_date $new_time");

    if ($selectedDate < $currentDate) {
        echo "<script>alert('The selected date and time cannot be in the past.'); window.location.href='orders.php';</script>";
        exit;
    }

    // Update the reservation in the database
    $query = "UPDATE reservation SET reservation_date = ?, reservation_time = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $new_date, $new_time, $reservation_id);

    if ($stmt->execute()) {
        echo "<script>alert('Reservation rescheduled successfully.'); window.location.href='orders.php';</script>";
    } else {
        echo "<script>alert('Error rescheduling reservation: " . $stmt->error . "'); window.location.href='orders.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='orders.php';</script>";
}
?>