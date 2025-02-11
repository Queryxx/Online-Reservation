<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'conn.php';
require 'vendor/autoload.php';

$pin = sprintf("%06d", rand(0, 999999));
$_SESSION['delete_pin'] = $pin;
$_SESSION['delete_pin_expiry'] = time() + (15 * 60);

// Get user email
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Send PIN via email using your existing PHPMailer configuration
$mail = new PHPMailer(true);
// ... Your existing PHPMailer setup ...

echo json_encode(['success' => true]);