<?php
include 'conn.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone']; // Add phone number
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Add and hash password

    // Insert user data into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $password); // Include phone number and password

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Sorry, there was an error adding the user.";
    }

    $stmt->close();
    $conn->close();
}
?>