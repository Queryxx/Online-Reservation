<?php
session_start();
include 'conn.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$unread_count = $stmt->get_result()->fetch_row()[0];

header('Content-Type: application/json');
echo json_encode(['unread_count' => $unread_count]);

$conn->close();
?>