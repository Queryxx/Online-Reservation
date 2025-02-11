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

    .sidebar-backdrop.active {
        opacity: 1;
        visibility: visible;
    }
    .sidebar .profile-picture {
    flex-shrink: 0;
}

.sidebar .ml-4 {
    min-width: 0; /* Allows truncation to work properly */
}

.sidebar .truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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

    .logout-link .logout-text {
        display: inline;
    }

    .logout-link .logout-icon {
        display: none;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 0;
        }

        .back-to-home-text {
            display: none;
        }

        .back-to-home-icon {
            display: inline;
        }

        .logout-link .logout-text {
            display: none;
        }

        .logout-link .logout-icon {
            display: inline;
        }
    }

    @media (min-width: 769px) {
        .back-to-home-icon {
            display: none;
        }

        .logout-link::before {
            content: "\f2f5";
            /* Font Awesome sign-out-alt icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 0.5rem;
            margin-left: 15px;
        }

        .logout-link .logout-icon {
            display: none;
        }
    }
</style>

<body class="bg-gradient-to-r font-sans">

    <!-- Header -->
    <header class="header py-4 px-6">
        <div class="max-w-screen-xl mx-auto flex justify-between items-center">
            <button id="menu-toggle" class="md:hidden text-red-600">
                <i class="fas fa-bars"></i>
            </button>

            <a href="index.php" class="text-red-600 hover:text-red-800 flex items-center">
                <i class="fas fa-arrow-left mr-2 back-to-home-text"></i>
                <span class="back-to-home-text">Back to Home</span>
                <i class="fas fa-home back-to-home-icon"></i>
            </a>

            <h1 class="text-2xl font-semibold text-red-600">My Account</h1>

            <a href="logout.php" class="logout-link text-red-600 hover:text-red-800">
                <span class="logout-text">Logout</span>
                <i class="fas fa-sign-out-alt logout-icon"></i>
            </a>
        </div>
    </header>
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

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

    <!-- Add this JavaScript at the bottom of the file -->
    <script>
        const notificationBtn = document.getElementById('notification-btn');
        const notificationDropdown = document.getElementById('notification-dropdown');

        notificationBtn.addEventListener('click', () => {
            notificationDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });
    </script>
</body>