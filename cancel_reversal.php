<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['reservation_id'])) {
    header('Location: orders.php');
    exit();
}

$reservation_id = $_GET['reservation_id'];
$user_id = $_SESSION['user_id'];

// Verify ownership and status
$stmt = $conn->prepare("SELECT user_id, status FROM reservation WHERE reservation_id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();

if ($reservation && $reservation['user_id'] == $user_id && $reservation['status'] === 'Pending Cancellation') {
    // Update status back to Pending
    $update = $conn->prepare("UPDATE reservation SET status = 'Pending', cancel_reason = NULL WHERE reservation_id = ?");
    $update->bind_param("i", $reservation_id);
    $update->execute();
}

header('Location: orders.php');
exit();
?>