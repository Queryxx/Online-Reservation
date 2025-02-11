<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Assuming you have a function to get user details
include 'conn.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();
$conn->close();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        // Update profile picture in database
        include 'conn.php';
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        if ($stmt->execute()) {
            echo "The file " . htmlspecialchars(basename($_FILES["profile_picture"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error updating your profile picture.";
        }
        $stmt->close();
        $conn->close();

        // Refresh the page to show the new profile picture
        header("Location: userdashboard.php");
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Handle profile picture removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_picture'])) {
    // Update profile picture in database to null
    include 'conn.php';
    $stmt = $conn->prepare("UPDATE users SET profile_picture = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "Profile picture has been removed.";
    } else {
        echo "Sorry, there was an error removing your profile picture.";
    }
    $stmt->close();
    $conn->close();

    // Refresh the page to show the default profile picture
    header("Location: userdashboard.php");
    exit();
}
?>