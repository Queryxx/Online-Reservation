<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $event_start = $_POST['event_start'];

    // Validate inputs
    if (empty($reservation_id) || empty($event_start)) {
        echo "<script>alert('All fields are required.'); window.location.href='rescater.php';</script>";
        exit;
    }

    // Check if the new date and time are in the future
    $currentDate = new DateTime();
    $selectedDate = new DateTime($event_start);

    // Ensure the selected date is at least one week in the future
    $oneWeekFromNow = (new DateTime())->modify('+1 week');

    if ($selectedDate < $currentDate) {
        echo "<script>alert('The selected date and time cannot be in the past.'); window.location.href='rescater.php';</script>";
        exit;
    } elseif ($selectedDate < $oneWeekFromNow) {
        echo "<script>alert('The selected date and time must be at least one week in advance.'); window.location.href='rescater.php';</script>";
        exit;
    }

    // Update the reservation with only event_start
    $query = "UPDATE reservation_catering SET event_start = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $event_start, $reservation_id);

    if ($stmt->execute()) {
        echo "<script>alert('Reservation rescheduled successfully.'); window.location.href='rescater.php';</script>";
    } else {
        echo "<script>alert('Error rescheduling reservation: " . $stmt->error . "'); window.location.href='rescater.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.location.href='rescater.php';</script>";
}
?>