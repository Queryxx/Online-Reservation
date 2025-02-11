<?php
session_start();
include 'conn.php'; // Ensure your database connection is included

// Get current password from POST request
$currentPassword = $_POST['current_password'];

// Fetch the logged-in user's password from the database
$userEmail = $_SESSION['user_email'];
$query = $conn->prepare("SELECT password FROM users WHERE email = ?");
$query->bind_param('s', $userEmail);
$query->execute();
$query->bind_result($hashedPassword);
$query->fetch();
$query->close();

// Verify the entered password
if (password_verify($currentPassword, $hashedPassword)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
