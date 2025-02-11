<?php
include 'conn.php'; // Include the database connection

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the manage users page
    header("Location: manage_users.php");
    exit();
} else {
    echo "Invalid user ID.";
}
?>