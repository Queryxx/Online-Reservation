
    <!-- Dashboard Content -->
    <div class="content" id="content">
        <div class="container mt-4">
            <h1>Welcome to the Admin Dashboard</h1>
            <div class="row mt-4">
               <!-- Cards -->
<div class="col-md-4">
    <?php include 'reserve.php'; ?>
    <div class="card text-white bg-danger mb-3 admin-dashboard-card-reservations">
        <div class="card-header">Reservations</div>
        <div class="card-body">
            <h5 class="card-title"><?php echo $activeReservations; ?> Active Reservation</h5>
            <p class="card-text">Manage all reservations here.</p>
            <a href="manage_reservation.php" class="btn btn-light">View Details</a>
        </div>
    </div>
</div>

<div class="col-md-4">
    <?php include 'users.php'; ?>
    <div class="card text-white bg-success mb-3 admin-dashboard-card-users">
        <div class="card-header">Users</div>
        <div class="card-body">
            <h5 class="card-title"><?php echo $registeredUsers; ?> Registered Users</h5>
            <p class="card-text">Manage all users here.</p>
            <a href="manage_users.php" class="btn btn-light">Manage Users</a>
        </div>
    </div>
</div>

<div class="col-md-4">
    <?php include 'menus.php'; ?>
    <div class="card text-white bg-warning mb-3 admin-dashboard-card-menu">
        <div class="card-header">Menu</div>
        <div class="card-body">
            <h5 class="card-title"><?php echo $menuItems; ?> Menu Items</h5>
            <p class="card-text">Track all Menu here.</p>
            <a href="manage_menu.php" class="btn btn-light">View Menu</a>
        </div>
    </div>
</div>

            </div>
        </div>
        <style>
            /* Unique hover effect on the cards */
.admin-dashboard-card-reservations,
.admin-dashboard-card-users,
.admin-dashboard-card-menu {
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
}

/* Hover state for the card */
.admin-dashboard-card-reservations:hover,
.admin-dashboard-card-users:hover,
.admin-dashboard-card-menu:hover {
    transform: scale(1.05); /* Slightly enlarge the card */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
}

/* Optionally, add an effect for the button on hover */
.admin-dashboard-card-reservations .btn:hover,
.admin-dashboard-card-users .btn:hover,
.admin-dashboard-card-menu .btn:hover {
    background-color:rgb(27, 151, 5); /* Darker color on button hover */
    color: white; /* White text on hover */
}

        </style>