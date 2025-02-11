<?php
session_start();

// Set session timeout duration (in seconds)
$timeout_duration = 180; // 3 minutes

// Check if the last activity timestamp is set
if (isset($_SESSION['LAST_ACTIVITY'])) {
    // Calculate the session lifetime
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
    // If the session has expired, destroy the session and redirect to the login page
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}

// Update the last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
include 'conn.php'; // Assuming you have a connection to the database
$notifications_query = "
    SELECT 
        'reservation' as type,
        r.reservation_id as id,
        r.status,
        r.created_at,
        r.is_read,
        u.name as user_name
    FROM reservation r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.status IN ('Pending', 'Pending Cancellation')
    
    UNION
    
    SELECT 
        'catering' as type,
        rc.id as id,
        rc.status,
        rc.created_at,
        rc.is_read, -- Changed from hardcoded 0
        u.name as user_name
    FROM reservation_catering rc
    JOIN users u ON rc.user_id = u.user_id
    WHERE rc.status = 'Pending'
    
    UNION
    
    SELECT 
        'message' as type,
        m.message_id as id,
        'Unread' as status,
        m.created_at,
        m.is_read,
        u.name as user_name
    FROM contact_messages m
    JOIN users u ON m.user_id = u.user_id
    WHERE m.is_read = 0
    ORDER BY created_at DESC LIMIT 5
";

$stmt = $conn->prepare($notifications_query);
$stmt->execute();
$notifications_result = $stmt->get_result();
$notifications = $notifications_result->fetch_all(MYSQLI_ASSOC);

// Update notification count logic to include catering
$notification_count = array_reduce($notifications, function ($carry, $item) {
    if ($item['type'] == 'reservation' || $item['type'] == 'catering') {
        return $carry + (($item['is_read'] == 0 &&
            ($item['status'] == 'Pending' || $item['status'] == 'Pending Cancellation')) ? 1 : 0);
    } else { // message type
        return $carry + ($item['is_read'] == 0 ? 1 : 0);
    }
}, 0);

$pending_reservations = $notifications;

$query = "
    SELECT 
        r.reservation_id, r.user_id, r.reservation_date, r.reservation_time, r.dine_in_or_takeout, r.takeout_type, r.delivery_location, r.guests, r.payment_method, r.special_requests, r.status, r.created_at,
        u.name AS user_name, u.email AS user_email, u.phone,  
        GROUP_CONCAT(m.name SEPARATOR ', ') AS menu_items,
        GROUP_CONCAT(m.image_url SEPARATOR ', ') AS menu_images,
        GROUP_CONCAT(m.price SEPARATOR ', ') AS menu_prices
    FROM 
        reservation r
    JOIN 
        users u ON r.user_id = u.user_id
    LEFT JOIN 
        reservation_menu rm ON r.reservation_id = rm.reservation_id
    LEFT JOIN 
        menu m ON rm.menu_item_id = m.id
    GROUP BY 
        r.reservation_id
    ORDER BY 
        r.created_at DESC
";

$result = $conn->query($query);
$reservations = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Dashboard for Victoria Grill Restaurant">
    <meta name="keywords" content="Admin, Dashboard, Victoria Grill, Restaurant">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-weight: 600;
            color: #333;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 80px;
            height: 100%;
            background-color: #800000;
            color: white;
            padding-top: 70px;
            transition: left 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
            /* Add this line to make the sidebar scrollable */
        }

        .sidebar .nav-link {
            color: #ddd;
            text-align: center;
            font-size: 1.2rem;
            padding: 15px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgb(219, 34, 9);
        }

        .sidebar .active {
            background-color: rgb(210, 39, 0);
        }

        .sidebar.hidden {
            left: -80px;
        }

        .content {
            margin-left: 80px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .content.expanded {
            margin-left: 0;
        }

        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 30px;
            font-size: 20px;
            color: white;
            cursor: pointer;
            z-index: 1100;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -80px;
            }

            .content {
                margin-left: 0;
            }

            .sidebar.hidden {
                left: 0;
            }

            .content.expanded {
                margin-left: 80px;
            }
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: rgb(187, 9, 9);
            color: white;
        }

        .header {
            background: linear-gradient(90deg, #800000, rgb(228, 11, 4));
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .header img {
            height: 50px;
            margin-right: 20px;
            z-index: 1000;
        }

        .header h2 {
            margin: 0;
        }

        .notification-wrapper {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .notification-btn {
            color: white;
            background: transparent;
            border: none;
            font-size: 1.2rem;
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ff4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .dropdown-menu {
            min-width: 280px;
            padding: 0.5rem 0;
            margin: 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .notification-item {
            padding: 5px 0;
        }

        .notification-item .small {
            font-size: 0.8rem;
            color: #666;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item:active {
            background-color: #dc3545;
            color: white;
        }

        /* Add to existing CSS */
        .cancelled-notification {
            background-color: #fff3f3;
        }

        .cancelled-notification i {
            color: #dc3545;
        }

        .cancelled-notification:hover {
            background-color: #ffe6e6;
        }
    </style>
</head>

<body>
    <!-- Replace the existing header div with this -->
    <div class="header text-center">
        <img src="../victoria.jpg" alt="Logo" class="logo img-fluid rounded-circle">
        <h2 class="custom-font"><strong>Victoria Grill Restaurant</strong></h2>

        <div class="notification-wrapper">
    <div class="dropdown">
        <button class="btn notification-btn" type="button" id="notificationDropdown" data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="fas fa-bell"></i>
            <?php if ($notification_count > 0): ?>
                <span class="notification-badge"><?php echo $notification_count; ?></span>
            <?php endif; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
            <li><h6 class="dropdown-header">Recent Notifications</h6></li>
            <li><hr class="dropdown-divider"></li>

            <?php
            $unread_notifications = array_filter($notifications, function ($notification) {
                return $notification['is_read'] == 0;
            });

            if (!empty($unread_notifications)): ?>
                <?php foreach ($unread_notifications as $notification): ?>
                    <li>
                        <a class="dropdown-item"
                            href="<?php 
                                if ($notification['type'] === 'message') {
                                    echo 'view_message.php?id=';
                                } elseif ($notification['type'] === 'catering') {
                                    echo 'view_catering.php?id=';
                                } else {
                                    echo 'view_notif.php?id=';
                                }
                            ?><?php echo $notification['id']; ?>&status=<?php echo $notification['status']; ?>"
                            onclick="return markAsRead('<?php echo $notification['type']; ?>', <?php echo $notification['id']; ?>);"
                            data-notification-id="<?php echo $notification['id']; ?>"
                            data-notification-type="<?php echo $notification['type']; ?>">
                            <div class="notification-item">
                                <?php if ($notification['type'] === 'message'): ?>
                                    <i class="fas fa-envelope text-primary"></i>
                                    <strong><?php echo htmlspecialchars($notification['user_name']); ?></strong> sent a message
                                <?php elseif ($notification['type'] === 'catering'): ?>
                                    <i class="fas fa-utensils text-info"></i>
                                    <strong><?php echo htmlspecialchars($notification['user_name']); ?></strong> has a new catering request
                                <?php else: ?>
                                    <?php if ($notification['status'] === 'Pending Cancellation'): ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                        <strong><?php echo htmlspecialchars($notification['user_name']); ?></strong> has requested to cancel their reservation
                                    <?php else: ?>
                                        <i class="fas fa-clock text-warning"></i>
                                        <strong><?php echo htmlspecialchars($notification['user_name']); ?></strong> has a new reservation
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="text-muted small">
                                    <?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><span class="dropdown-item">No new notifications</span></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-center bg-light fw-bold" href="all_notification.php">
                    See All Notifications <i class="fas fa-arrow-right"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
    </div>
    <!-- can you put notification here put it in top right corner -->
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="dashboard.php" class="nav-link active text-center"><i class="fas fa-home"></i></a>
        <a href="manage_hero.php" class="nav-link text-center"><i class="fas fa-images"></i></a>
        <a href="manage_users.php" class="nav-link text-center"><i class="fas fa-users"></i></a>
        <a href="reservation_manager.php" class="nav-link text-center"><i class="fas fa-calendar-check"></i></a>
        <a href="menu_manager.php" class="nav-link text-center"><i class="fas fa-concierge-bell"></i></a>
        <a href="manage_promotion.php" class="nav-link text-center"><i class="fas fa-gift"></i></a>
        <a href="manage_contact.php" class="nav-link text-center"><i class="fas fa-inbox"></i></a>
        <a href="manage_about.php" class="nav-link text-center"><i class="fas fa-info-circle"></i></a>
        <a href="manage_reviews.php" class="nav-link text-center"><i class="fas fa-comments"></i></a>
        <a href="settings.php" class="nav-link text-center"><i class="fas fa-cogs"></i></a>
        <a href="adminlogout.php" class="nav-link text-center"><i class="fas fa-sign-out-alt"></i></a>
    </div>

    <!-- Toggle Button -->
    <div class="toggle-btn" id="toggleSidebarBtn">
        <i class="fas fa-bars"></i>
    </div>
    <!-- Dashboard Cards -->
    <?php include 'dashboardcontent.php'; ?>

    <!-- Sections -->
    <?php include 'pie.php'; ?>
    <?php include 'reservationsection.php'; ?>
    <?php include 'menucard.php'; ?>
    <?php include 'promosection.php'; ?>

    </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <!-- Script for Sidebar Toggle -->
    <script>
        const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            content.classList.toggle('expanded');
        });
    </script>
</body>

</html>