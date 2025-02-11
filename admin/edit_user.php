<?php
include 'conn.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if password fields are filled and match
    if (!empty($password) && $password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $hashed_password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
    }

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Sorry, there was an error updating the user.";
    }

    $stmt->close();
    $conn->close();
}
?>