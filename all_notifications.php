<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all notifications with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total notifications count
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_notifications = $stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total_notifications / $per_page);

// Get notifications for current page
$stmt = $conn->prepare("
    SELECT id, message, type, created_at, is_read, reference_id 
    FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user_id, $per_page, $offset);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get unread count
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$unread_count = $stmt->get_result()->fetch_row()[0];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
               <!-- Add back button -->
               <a href="userdashboard.php" class="inline-block mb-4 text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b flex justify-between items-center">
                    <h1 class="text-2xl font-semibold">All Notifications</h1>
                    <?php if ($unread_count > 0): ?>
                        <button id="mark-all-read" class="text-blue-600 hover:text-blue-800">
                            Mark all as read
                        </button>
                    <?php endif; ?>
                </div>

                <div class="divide-y">
                    <?php if (empty($notifications)): ?>
                        <div class="p-4 text-center text-gray-500">
                            No notifications
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <div class="notification-item p-4 <?php echo $notif['is_read'] ? 'bg-white' : 'bg-blue-50'; ?>"
                                 data-id="<?php echo $notif['id']; ?>">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <?php
                                            $labelClass = '';
                                            $labelText = '';
                                            switch ($notif['type']) {
                                                case 'reservation':
                                                    $labelClass = 'bg-blue-100 text-blue-800';
                                                    $labelText = 'Reservation';
                                                    break;
                                                case 'order':
                                                    $labelClass = 'bg-green-100 text-green-800';
                                                    $labelText = 'Order';
                                                    break;
                                                case 'status':
                                                    $labelClass = 'bg-yellow-100 text-yellow-800';
                                                    $labelText = 'Status Update';
                                                    break;
                                                case 'payment':
                                                    $labelClass = 'bg-purple-100 text-purple-800';
                                                    $labelText = 'Payment';
                                                    break;
                                                default:
                                                    $labelClass = 'bg-gray-100 text-gray-800';
                                                    $labelText = 'Notification';
                                            }
                                            ?>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $labelClass; ?>">
                                                <?php echo $labelText; ?>
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                <?php echo date('M d, Y h:i A', strtotime($notif['created_at'])); ?>
                                            </span>
                                        </div>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($notif['message']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="p-4 border-t">
                        <div class="flex justify-center gap-2">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="px-3 py-1 rounded <?php echo $page === $i ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Mark individual notification as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notifId = this.dataset.id;
                if (!this.classList.contains('bg-white')) {
                    fetch(`mark_notification_read.php?id=${notifId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.classList.remove('bg-blue-50');
                                this.classList.add('bg-white');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });

        // Mark all notifications as read
        document.getElementById('mark-all-read')?.addEventListener('click', function() {
            fetch('mark_notifications_read.php', {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.classList.remove('bg-blue-50');
                            item.classList.add('bg-white');
                        });
                        this.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>