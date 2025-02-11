<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['review']; // Changed to match DB column name

    // Validate input
    if (empty($reservation_id) || empty($rating) || empty($comment)) {
        echo "<script>
            alert('Please fill all the fields.');
            window.location.href='reviews.php';
        </script>";
        exit;
    }

    try {
        // Prepare and execute the insert statement
        $query = "INSERT INTO reviews (reservation_id, rating, comment, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $reservation_id, $rating, $comment);

        if ($stmt->execute()) {
            echo "<script>
                alert('Review submitted successfully.');
                window.location.href='reviews.php';
            </script>";
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href='reviews.php';
        </script>";
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    echo "<script>
        alert('Invalid request method.');
        window.location.href='reviews.php';
    </script>";
}
?>