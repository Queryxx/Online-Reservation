delete_cateringres.php<?php
include 'conn.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $reservation_id = $_GET['id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
      
        // Delete notification reads first
        $query_delete_notif = "DELETE FROM notification_reads WHERE id = ?";
        $stmt_delete_notif = $conn->prepare($query_delete_notif);
        $stmt_delete_notif->bind_param("i", $reservation_id);
        $stmt_delete_notif->execute();
        $stmt_delete_notif->close();
        
        // Delete the reservation
        $query_delete = "DELETE FROM reservation_catering WHERE id = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->bind_param("i", $reservation_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        // Commit transaction
        $conn->commit();
        
        header("Location: manage_cateringres.php");
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