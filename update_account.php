<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'conn.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name']);

    // Basic validation
    if (empty($name)) {
        $_SESSION['error'] = "Name is required.";
        header('Location: account_info.php');
        exit();
    }

    // Update user name only
    $stmt = $conn->prepare("UPDATE users SET name = ? WHERE user_id = ?");
    $stmt->bind_param("si", $name, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Name updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating name.";
    }

    $stmt->close();
    $conn->close();

    header('Location: account_info.php');
    exit();
}
?>