<?php
session_start();
require_once 'conn.php';

$response = ['success' => false, 'unread_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];
    $user_id = $_SESSION['user_id'];

    // Mark notification as read
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    
    if ($stmt->execute()) {
        // Get updated unread count
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $countStmt->bind_param("i", $user_id);
        $countStmt->execute();
        $response['unread_count'] = $countStmt->get_result()->fetch_row()[0];
        $response['success'] = true;
    }
}

echo json_encode($response);
?>