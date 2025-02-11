<?php
include 'conn.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $reservation_id = $_GET['id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete related reviews first
        $query_delete_reviews = "DELETE FROM reviews WHERE reservation_id = ?";
        $stmt_delete_reviews = $conn->prepare($query_delete_reviews);
        $stmt_delete_reviews->bind_param("i", $reservation_id);
        $stmt_delete_reviews->execute();
        $stmt_delete_reviews->close();
        
        // Delete notification reads first
        $query_delete_notif = "DELETE FROM notification_reads WHERE reservation_id = ?";
        $stmt_delete_notif = $conn->prepare($query_delete_notif);
        $stmt_delete_notif->bind_param("i", $reservation_id);
        $stmt_delete_notif->execute();
        $stmt_delete_notif->close();
        
        // Delete reservation menu items if they exist
        $query_delete_menu = "DELETE FROM reservation_menu WHERE reservation_id = ?";
        $stmt_delete_menu = $conn->prepare($query_delete_menu);
        $stmt_delete_menu->bind_param("i", $reservation_id);
        $stmt_delete_menu->execute();
        $stmt_delete_menu->close();
        
        // Delete the reservation
        $query_delete = "DELETE FROM reservation WHERE reservation_id = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->bind_param("i", $reservation_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        // Commit transaction
        $conn->commit();
        
        header("Location: manage_reservation.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        die('Delete failed: ' . htmlspecialchars($e->getMessage()));
    }
} else {
    echo "Invalid reservation ID.";
}
?>