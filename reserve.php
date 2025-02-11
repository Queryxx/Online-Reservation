<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['id'];

// Get reservation details
$stmt = $conn->prepare("
    SELECT r.*, 
           GROUP_CONCAT(
               CONCAT(m.name, ' (', rm.quantity, ')')
               SEPARATOR ', '
           ) as menu_items,
           SUM(m.price * rm.quantity) as total_amount
    FROM reservation r
    LEFT JOIN reservation_menu rm ON r.reservation_id = rm.reservation_id
    LEFT JOIN menu m ON rm.menu_item_id = m.id
    WHERE r.reservation_id = ? AND r.user_id = ?
    GROUP BY r.reservation_id
");

$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();

if (!$reservation) {
    header('Location: userdashboard.php');
    exit();
}
if (isset($_GET['notif_id'])) {
    $notif_id = $_GET['notif_id'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $user_id);
    $stmt->execute();
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="userdashboard.php" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Reservation #<?php echo $reservation['reservation_id']; ?></h1>
                    <p class="text-gray-600">Created on <?php echo date('F j, Y', strtotime($reservation['created_at'])); ?></p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    <?php echo match($reservation['status']) {
                        'Pending' => 'bg-yellow-100 text-yellow-800',
                        'Confirmed' => 'bg-green-100 text-green-800',
                        'Cancelled' => 'bg-red-100 text-red-800',
                        'Completed' => 'bg-blue-100 text-blue-800',
                        default => 'bg-gray-100 text-gray-800'
                    }; ?>">
                    <?php echo $reservation['status']; ?>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-lg font-semibold mb-3">Reservation Details</h2>
                    <div class="space-y-2">
                        <p><span class="text-gray-600">Date:</span> <?php echo date('F j, Y', strtotime($reservation['reservation_date'])); ?></p>
                        <p><span class="text-gray-600">Time:</span> <?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?></p>
                        <p><span class="text-gray-600">Guests:</span> <?php echo $reservation['guests']; ?> persons</p>
                        <p><span class="text-gray-600">Type:</span> <?php echo $reservation['dine_in_or_takeout']; ?></p>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold mb-3">Order Summary</h2>
                    <div class="space-y-2">
                        <p><span class="text-gray-600">Menu Items:</span> <?php echo $reservation['menu_items']; ?></p>
                        <p><span class="text-gray-600">Total Amount:</span> ₱<?php echo number_format($reservation['total_amount'], 2); ?></p>
                    </div>
                </div>
            </div>

            <?php if ($reservation['status'] === 'Pending'): ?>
            <div class="flex space-x-4">
                <form action="update_reservation.php" method="POST" class="inline">
                    <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Cancel Reservation
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
    // Automatically mark notification as read when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const notifId = urlParams.get('notif_id');
        
        if (notifId) {
            fetch(`mark_notification_read.php?id=${notifId}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Failed to mark notification as read');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>
</body>
</html>