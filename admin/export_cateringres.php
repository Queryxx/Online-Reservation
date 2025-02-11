<?php include 'servercater.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations - Victoria Grill Restaurant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            background-color:#800000;
            color: white;
            padding-top: 70px;
            transition: transform 0.3s ease;
            z-index: 1050;
            transform: translateX(-100%);
            overflow-y: auto; /* Add this line to make the sidebar scrollable */
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .nav-link {
            color: #ddd;
            text-align: center;
            font-size: 1.2rem;
            padding: 15px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .nav-link:hover {
            color: #fff;
            background-color: rgb(219, 34, 9);
        }
        .nav-link.active {
            background-color: rgb(219, 34, 9);
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
            background-color:rgb(219, 34, 9);
        }

        .sidebar .active {
            background-color:rgb(219, 34, 9);
        }

        .sidebar.hidden {
            left: -80px;
        }

        .content {
            padding: 20px;
            margin-left: 80px;
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
            .content {
                margin-left: 0;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
        }

        .header {
            background:linear-gradient(90deg, #800000,rgb(228, 11, 4));
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
    </style>
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <div class="header text-center"> <img src="../Victoria.jpg" alt="Logo" class="logo img-fluid rounded-circle">
        <h2 class="custom-font"><strong>Victoria Grill Restaurant</strong></h2>
    </div>
    <!-- Toggle Button -->
    <div class="toggle-btn" id="toggleSidebarBtn">
        <i class="fas fa-bars"></i>
    </div>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="dashboard.php" class="nav-link text-center"><i class="fas fa-home"></i></a>
        <a href="manage_hero.php" class="nav-link text-center"><i class="fas fa-images"></i></a>
        <a href="manage_users.php" class="nav-link text-center"><i class="fas fa-users"></i></a>
        <a href="reservation_manager.php" class="nav-link active text-center"><i class="fas fa-calendar-check"></i></a>
        <a href="menu_manager.php" class="nav-link text-center"><i class="fas fa-concierge-bell"></i></a>
        <a href="manage_promotion.php" class="nav-link text-center"><i class="fas fa-gift"></i></a>
        <a href="manage_contact.php" class="nav-link text-center"><i class="fas fa-inbox"></i></a>
        <a href="manage_about.php" class="nav-link text-center"><i class="fas fa-info-circle"></i></a>
        <a href="manage_reviews.php" class="nav-link text-center"><i class="fas fa-comments"></i></a>
        <a href="settings.php" class="nav-link text-center"><i class="fas fa-cogs"></i></a>
        <a href="adminlogout.php" class="nav-link text-center"><i class="fas fa-sign-out-alt"></i></a>
    </div>

    <!-- Dashboard Content -->
    <div class="content" id="content">
        <section id="reservations" class="mt-4">
            <h2 class="mb-4"><strong>Manage Reservation</strong></h2>
            <div class="tabs">
    <ul class="flex border-b border-gray-200">
        <li class="-mb-px mr-1">
            <a class="tab-link inline-flex items-center px-4 py-2 font-semibold rounded-t-lg transition-all duration-200"
                href="#recent" data-tab="recent">
                <i class="fas fa-clock mr-2"></i> Recent
            </a>
        </li>
        <li class="-mb-px mr-1">
            <a class="tab-link inline-flex items-center px-4 py-2 font-semibold rounded-t-lg transition-all duration-200"
                href="#pending" data-tab="pending">
                <i class="fas fa-hourglass-half mr-2"></i> Pending
            </a>
        </li>
        <li class="mr-1">
            <a class="tab-link inline-flex items-center px-4 py-2 font-semibold rounded-t-lg transition-all duration-200"
                href="#confirmed" data-tab="confirmed">
                <i class="fas fa-check mr-2"></i> Confirmed
            </a>
        </li>
        <li class="mr-1">
            <a class="tab-link inline-flex items-center px-4 py-2 font-semibold rounded-t-lg transition-all duration-200"
                href="#rejected" data-tab="rejected">
                <i class="fas fa-times mr-2"></i> Rejected
            </a>
        </li>
        <li class="mr-1">
            <a class="tab-link inline-flex items-center px-4 py-2 font-semibold rounded-t-lg transition-all duration-200"
                href="#cancelled" data-tab="cancelled">
                <i class="fas fa-ban mr-2"></i> Cancelled
            </a>
        </li>
    </ul>
</div>

<style>
.tab-link {
    background-color: #ffffff;
    color: #3b82f6;
    border: 1px solid transparent;
}

.tab-link:hover:not(.active) {
    background-color: #f0f9ff;
    color: #1e40af;
    border-color: #e5e7eb;
}

.tab-link.active {
    background: linear-gradient(135deg,rgb(255, 81, 0) 0%,rgb(211, 19, 2) 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-link');
    
    function setActiveTab() {
        const hash = window.location.hash || '#recent';
        tabs.forEach(tab => {
            if(tab.getAttribute('href') === hash) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    window.addEventListener('hashchange', setActiveTab);
    setActiveTab();
});
</script>
            <!-- Reservations Cards -->
            <div class="table-responsive">
                <?php include 'recent_cater.php'; ?>
                <?php include 'pending_cater.php'; ?>
                <?php include 'confirmed_cater.php'; ?>
                <?php include 'rejected_cater.php'; ?>
                <?php include 'cancelled_cater.php'; ?>
                <?php include 'style.php'; ?>
            </div>
        </section>

        <script>
         const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');

        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

            function confirmDeletion(reservationId) {
                var confirmation = confirm('Are you sure you want to delete this reservation?');
                if (confirmation) {
                    window.location.href = 'delete_cateringres.php?id=' + reservationId;
                }
                return false; // Prevent the default link behavior
            }

            // Tab functionality
            document.querySelectorAll('.tabs a').forEach(tab => {
                tab.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.querySelector(this.getAttribute('href')).classList.remove('hidden');
                    document.querySelectorAll('.tabs a').forEach(tab => {
                        tab.classList.remove('border-l', 'border-t', 'border-r', 'rounded-t',
                            'text-blue-700');
                        tab.classList.add('text-blue-500');
                    });
                    this.classList.add('border-l', 'border-t', 'border-r', 'rounded-t', 'text-blue-700');
                    this.classList.remove('text-blue-500');
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>