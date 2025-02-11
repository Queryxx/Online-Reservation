<?php

include 'conn.php';


$user_id = $_SESSION['user_id'];  // Move this up before queries

// Get unread notifications count
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$unread_count = $stmt->get_result()->fetch_row()[0];

// Get recent notifications
$stmt = $conn->prepare("
    SELECT id, message, type, created_at, is_read, reference_id 
    FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get counts for overview cards
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservation WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_orders = $stmt->get_result()->fetch_row()[0];

$stmt = $conn->prepare("SELECT COUNT(*) FROM reservation WHERE user_id = ? AND status IN ('Pending', 'Confirmed')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$active_reservations = $stmt->get_result()->fetch_row()[0];

// Get recent orders
$stmt = $conn->prepare("
    SELECT r.*, 
           GROUP_CONCAT(rm.menu_item_id) as menu_items,
           SUM(m.price * rm.quantity) as total_amount
    FROM reservation r
    LEFT JOIN reservation_menu rm ON r.reservation_id = rm.reservation_id
    LEFT JOIN menu m ON rm.menu_item_id = m.id
    WHERE r.user_id = ?
    GROUP BY r.reservation_id
    ORDER BY r.created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get upcoming reservations
$stmt = $conn->prepare("
    SELECT *
    FROM reservation 
    WHERE user_id = ? 
    AND status IN ('Pending', 'Confirmed')
    AND reservation_date >= CURDATE()
    ORDER BY reservation_date ASC
    LIMIT 3
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcoming_reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user details
include 'conn.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();
$conn->close();

// Set profile picture in session
$_SESSION['profile_picture'] = $profile_picture;

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $filename = uniqid() . '_' . basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        include 'conn.php';
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        $_SESSION['profile_picture'] = $target_file;
        header("Location: userdashboard.php");
        exit();
    }
}

// Handle profile picture removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_picture'])) {
    include 'conn.php';
    $stmt = $conn->prepare("UPDATE users SET profile_picture = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $_SESSION['profile_picture'] = 'default.jpg';
    header("Location: userdashboard.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gradient-to-r font-sans">
    <style>
        body {
            background-color: aliceblue;
            font-family: 'Arial', sans-serif;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background-color: #800000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 40;
            padding-top: 80px;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out;
            z-index: 30;
        }

        .profile-picture {
            cursor: pointer;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.1);
        }

        .sidebar .profile-picture {
            flex-shrink: 0;
        }

        .sidebar .ml-4 {
            min-width: 0;
            /* Allows truncation to work properly */
        }

        .sidebar .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .main-content {
            margin-left: 280px;
            padding-top: 80px;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            z-index: 50;
            border-bottom: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Add these to your existing styles */
        #notification-dropdown {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        #notification-dropdown::-webkit-scrollbar {
            width: 6px;
        }

        #notification-dropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #notification-dropdown::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        @media (max-width: 768px) {

            .back-to-home-text,
            .logout-text {
                display: none;
            }

            .back-to-home .fa-arrow-left,
            .logout-link .fa-sign-out-alt {
                display: none;
            }

            .back-to-home::before {
                content: "\f015";
                /* Font Awesome home icon */
                font-family: "Font Awesome 5 Free";
                font-weight: 900;
                margin-right: 0.5rem;
            }

            .logout-link::before {
                content: "\f2f5";
                /* Font Awesome sign-out-alt icon */
                font-family: "Font Awesome 5 Free";
                font-weight: 900;
                margin-right: 0.5rem;
                margin-left: 15px;
            }
        }

        .notification-popup {
            transition: all 0.3s ease-in-out;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 640px) {
            .notification-popup {
                width: calc(100% - 2rem);
                margin-left: 1rem;
                margin-right: 1rem;
            }
        }
    </style>
    </head>

    <body class="bg-gradient-to-r font-sans">

        <!-- Header -->
        <header class="header py-4 px-6">
            <div class="max-w-screen-xl mx-auto flex justify-between items-center">
                <button id="menu-toggle" class="md:hidden text-red-600">
                    <i class="fas fa-bars"></i>
                </button>

                <a href="index.php" class="back-to-home text-red-600 hover:text-red-800">
                    <i class="fas fa-arrow-left mr-2"></i><span class="back-to-home-text">Back to Home</span>
                </a>

                <h1 class="text-2xl font-semibold text-red-600">My Account</h1>

                <div class="flex items-center space-x-4">
                    <!-- Notification Dropdown -->
                    <div class="relative">
                        <button id="notification-btn" class="text-blue-600 hover:text-blue-800 relative">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notification-badge" class="hidden absolute top-0 right-0 inline-block w-3 h-3 bg-red-600 rounded-full"></span>
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div id="notification-dropdown"
                            class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50">
                            <div class="p-4 border-b flex justify-between items-center">
                                <h3 class="text-lg font-semibold">Notifications</h3>
                                <?php if ($unread_count > 0): ?>
                                    <form action="mark_notifications_read.php" method="POST" class="inline">
                                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                            Mark all as read
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <?php if (empty($notifications)): ?>
                                    <div class="p-4 text-center text-gray-500">
                                        No notifications
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notif): ?>
                                        <a href="reserve.php?id=<?php echo $notif['reference_id']; ?>&notif_id=<?php echo $notif['id']; ?>"
                                            data-id="<?php echo $notif['id']; ?>"
                                            class="block p-4 hover:bg-gray-50 border-b <?php echo $notif['is_read'] ? 'bg-white' : 'bg-blue-50'; ?>">
                                            <div class="flex justify-between items-start mb-1">
                                                <div class="flex items-center">
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
                                                    <span
                                                        class="px-2 py-1 text-xs font-medium rounded-full <?php echo $labelClass; ?>">
                                                        <?php echo $labelText; ?>
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-400">
                                                    <?php echo timeAgo($notif['created_at']); ?>
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                <?php echo htmlspecialchars($notif['message']); ?>
                                            </p>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Always show See All link -->
                                <div class="p-4 border-t">
                                    <a href="all_notifications.php"
                                        class="block w-full text-center text-blue-600 hover:text-blue-800 font-semibold transition duration-150 ease-in-out">
                                        See All Notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a href="logout.php" class="logout-link text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt ml-16 mr-2"></i><span class="logout-text">Logout</span>
                        </a>
                    </div>
                </div>
        </header>
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <script>
            const notificationBtn = document.getElementById('notification-btn');
            const notificationDropdown = document.getElementById('notification-dropdown');
            const notificationBadge = document.getElementById('notification-badge');

            function updateNotificationDisplay(notifications) {
                const container = notificationDropdown.querySelector('.max-h-96');
                container.innerHTML = '';

                // Add notifications or no notifications message
                if (!notifications || notifications.length === 0) {
                    container.innerHTML = `
            <div class="p-4 text-center text-gray-500">
                No notifications
            </div>
        `;
                } else {
                    notifications.forEach(notif => {
                        const timeAgo = new Date(notif.created_at).toLocaleDateString();
                        const element = document.createElement('a');
                        element.href = `reserve.php?id=${notif.reference_id}`;
                        element.dataset.id = notif.id;
                        element.className = `block p-4 hover:bg-gray-50 border-b ${notif.is_read ? 'bg-white' : 'bg-blue-50'}`;
                        element.innerHTML = `
                <div class="flex justify-between items-start mb-1">
                    <div class="flex items-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getNotificationStyle(notif.type)}">
                            ${getNotificationType(notif.type)}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400">${timeAgo}</span>
                </div>
                <p class="text-sm text-gray-600">${notif.message}</p>
            `;
                        container.appendChild(element);
                        element.addEventListener('click', handleNotificationClick);
                    });
                }

                // Always add See All link
                const seeAllDiv = document.createElement('div');
                seeAllDiv.className = 'p-4 border-t';
                seeAllDiv.innerHTML = `
        <a href="all_notifications.php" 
           class="block w-full text-center text-blue-600 hover:text-blue-800 font-semibold">
            See All Notifications
        </a>
    `;
                container.appendChild(seeAllDiv);

                // Show or hide the badge based on unread notifications count
                const unreadCount = notifications.filter(notif => !notif.is_read).length;
                if (unreadCount > 0) {
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            }

            function getNotificationStyle(type) {
                switch (type) {
                    case 'reservation_approved':
                        return 'bg-green-100 text-green-800';
                    case 'reservation_rejected':
                        return 'bg-red-100 text-red-800';
                    case 'cancellation_approved':
                        return 'bg-yellow-100 text-yellow-800';
                    case 'cancellation_rejected':
                        return 'bg-orange-100 text-orange-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
                }
            }

            function getNotificationType(type) {
                switch (type) {
                    case 'reservation_approved':
                        return 'Approved';
                    case 'reservation_rejected':
                        return 'Rejected';
                    case 'cancellation_approved':
                        return 'Cancelled';
                    case 'cancellation_rejected':
                        return 'Cancel Rejected';
                    default:
                        return 'Notification';
                }
            }

            document.querySelector('form[action="mark_notifications_read.php"]')?.addEventListener('submit', function(e) {
                e.preventDefault();
                fetch('mark_notifications_read.php', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('#notification-dropdown .bg-blue-50').forEach(el => {
                                el.classList.remove('bg-blue-50');
                                el.classList.add('bg-white');
                            });
                            this.style.display = 'none';
                            notificationBadge.classList.add('hidden');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            function handleNotificationClick(e) {
                e.preventDefault();
                const notifId = this.dataset.id;
                const href = this.getAttribute('href');

                fetch(`mark_notification_read.php?id=${notifId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.remove('bg-blue-50');
                            this.classList.add('bg-white');
                            setTimeout(() => window.location.href = href, 100);
                            notificationBadge.classList.add('hidden');
                        } else {
                            window.location.href = href;
                        }
                    })
                    .catch(() => window.location.href = href);
            }

            function showNotificationPopup(message, type = 'info') {
                // Create popup element
                const popup = document.createElement('div');
                // Changed position to top-4 and added mx-auto and max-w-lg for centered positioning
                popup.className = `fixed top-5 left-1/2 transform -translate-x-1/2 p-4 rounded-lg shadow-lg z-50 max-w-lg w-full mx-4 translate-y-full opacity-0 transition-all duration-300 ${getBackgroundClass(type)}`;

                // Add content
                popup.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-white text-xl">${getIcon(type)}</span>
                <p class="text-sm font-medium text-white">${message}</p>
            </div>
            <button class="text-white hover:text-gray-200 focus:outline-none" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
                // Add to document
                document.body.appendChild(popup);

                // Trigger animation
                setTimeout(() => {
                    popup.classList.remove('translate-y-full', 'opacity-0');
                }, 100);

                // Remove after delay
                setTimeout(() => {
                    popup.classList.add('translate-y-full', 'opacity-0');
                    setTimeout(() => popup.remove(), 300);
                }, 5000);
            }

            function getBackgroundClass(type) {
                switch (type) {
                    case 'success':
                        return 'bg-green-600';
                    case 'error':
                        return 'bg-red-600';
                    case 'warning':
                        return 'bg-yellow-600';
                    default:
                        return 'bg-green-600';
                }
            }

            function getIcon(type) {
                switch (type) {
                    case 'success':
                        return '<i class="fas fa-check-circle"></i>';
                    case 'error':
                        return '<i class="fas fa-exclamation-circle"></i>';
                    case 'warning':
                        return '<i class="fas fa-exclamation-triangle"></i>';
                    default:
                        return '<i class="fas fa-calendar"></i>';
                }
            }
            // Update your existing notification checking code

            let lastNotificationId = localStorage.getItem('lastNotificationId') || null;
            let isInitialLoad = true;

            function checkNewNotifications(isManualCheck = false) {
                fetch('check_notifications.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        // Update dropdown display
                        updateNotificationDisplay(data.notifications);

                        // Only show popup for new notifications on automatic checks
                        if (!isManualCheck && data.notifications.length > 0) {
                            const unreadNotifications = data.notifications.filter(n => !n.is_read);
                            if (unreadNotifications.length > 0) {
                                const latestNotif = unreadNotifications[0];

                                // Only show popup if this is a new notification and not initial page load
                                if (lastNotificationId !== latestNotif.id && !isInitialLoad) {
                                    lastNotificationId = latestNotif.id;
                                    localStorage.setItem('lastNotificationId', lastNotificationId);
                                    showNotificationPopup(latestNotif.message, getNotificationType(latestNotif.type).toLowerCase());
                                }
                            }
                        }

                        // Set initial load to false after first check
                        isInitialLoad = false;
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Update click event listener
            notificationBtn.addEventListener('click', () => {
                notificationDropdown.classList.toggle('hidden');
                if (!notificationDropdown.classList.contains('hidden')) {
                    checkNewNotifications(true); // Pass true to indicate manual check
                }
            });

            // Update document click handler
            document.addEventListener('click', (e) => {
                if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });

            // Initial check and periodic updates
            checkNewNotifications();
            setInterval(() => checkNewNotifications(), 8000);
        </script>
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="px-6 mt-8">
                <!-- Profile Section -->
                <div class="flex items-center mb-6 relative">
                    <img src="<?= htmlspecialchars($profile_picture ? $profile_picture : 'default.jpg'); ?>"
                        alt="Profile Picture"
                        class="w-16 h-16 profile-picture rounded-full object-cover border-2 border-gray-300 hover:border-yellow-600"
                        id="profile-picture">
                    <div class="ml-4 max-w-[150px]"> <!-- Added max-width -->
                        <h2 class="text-lg font-semibold text-gray-100 truncate"><?= htmlspecialchars($name); ?></h2>
                        <p class="text-sm text-gray-100 truncate"><?= htmlspecialchars($email); ?></p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <ul class="space-y-4">
                    <li>
                        <a href="userdashboard.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-dashboard mr-3"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="account_info.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-user-circle mr-3"></i>Profile
                        </a>
                    </li>
                    <li>
                        <a href="account_menu.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-utensils mr-3"></i>Menu
                        </a>
                    </li>
                    <li>
                        <a href="orders.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-shopping-cart mr-3"></i>Orders
                        </a>
                    </li>
                    <li>
                        <a href="history.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-history mr-3"></i>History
                        </a>
                    </li>
                    <li>
                        <a href="reviews.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-comments mr-3"></i>Reviews
                        </a>
                    </li>
                    <li>
                        <a href="rescater.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-calendar mr-3"></i>Catering
                        </a>
                    </li>
                    <li>
                        <a href="messages.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-inbox mr-3"></i>Inbox
                        </a>
                    </li>
                    <li>
                        <a href="account_setting.php" class="flex items-center text-gray-100 hover:text-yellow-300">
                            <i class="fas fa-cog mr-3"></i>Account Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>