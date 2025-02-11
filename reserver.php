<?php
session_start();
include 'conn.php'; // Database connection

// Fetch menu items from the database
$query = "SELECT * FROM menu"; 
$result = $conn->query($query);
$menu_items = $result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = $_POST['guests'];
    $type = $_POST['type'];
    $takeoutType = isset($_POST['takeoutType']) ? $_POST['takeoutType'] : null;
    $location = isset($_POST['location']) ? $_POST['location'] : null;
    $payment = $_POST['payment'];
    $requests = isset($_POST['requests']) ? $_POST['requests'] : '';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $menu_items_selected = isset($_POST['menu_items']) ? $_POST['menu_items'] : [];

    // Debugging: Log important variables
    error_log("User ID: $user_id, Type: $type, Takeout Type: $takeoutType, Location: $location");
    error_log("Selected Menu Items: " . implode(", ", $menu_items_selected));

    // Insert reservation into the database
    $stmt = $conn->prepare("INSERT INTO reservation (user_id, reservation_date, reservation_time, dine_in_or_takeout, takeout_type, delivery_location, guests, payment_method, special_requests, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("issssssss", $user_id, $date, $time, $type, $takeoutType, $location, $guests, $payment, $requests);

    if ($stmt->execute()) {
        $reservation_id = $stmt->insert_id;
        // Insert selected menu items into the reservation_menu table
        $stmt_menu = $conn->prepare("INSERT INTO reservation_menu (reservation_id, menu_item_id) VALUES (?, ?)");
        foreach ($menu_items_selected as $menu_item_id) {
            $stmt_menu->bind_param("ii", $reservation_id, $menu_item_id);
            $stmt_menu->execute();
        }
        $stmt_menu->close();

        // Redirect to a confirmation page or display a success message
        header("Location: reservation_success.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    

    $stmt->close();
    $conn->close();
}
?>
