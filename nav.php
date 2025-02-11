<!-- Navbar -->
<nav class="bg-maroon-900 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <img src="victoria.jpg" alt="Victoria Grill Logo" class="h-12 w-12 rounded-full">
            <div class="flex flex-col justify-center">
                <h1 class="text-2xl font-bold text-white">Victoria Grill Restaurant</h1>
                <div class="flex items-center text-gray-300 text-xs text-yellow-400">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <span>Manzano Street, Zone 5, Bangued, Philippines</span>
                </div>
            </div>
        </div>
        <!-- Hamburger Icon (Mobile) -->
        <button id="hamburger" class="lg:hidden flex items-center text-white focus:outline-none">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Navigation Links -->
        <ul id="menu" class="hidden lg:flex space-x-6 items-center">
            <li><a href="index.php" class="text-white hover:bg-maroon-700 hover:text-yellow-400 p-2 rounded block"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#menuu" class="text-white hover:bg-maroon-700 hover:text-yellow-400  p-2 rounded block"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="#promotions" class="text-white hover:bg-maroon-700 hover:text-yellow-400  p-2 rounded block"><i class="fas fa-concierge-bell"></i> Promo</a></li>
            <li><a href="#about" class="text-white hover:bg-maroon-700 hover:text-yellow-400  p-2 rounded block"><i class="fas fa-calendar-check"></i> About Us</a></li>
            <li><a href="#contact" class="text-white hover:bg-maroon-700 hover:text-yellow-400  p-2 rounded block"><i class="fas fa-phone-alt"></i> Contact</a></li>
            <li><a href="choose.php" class="text-white bg-maroon-500 hover:text-yellow-400 px-4 py-2 rounded-md hover:bg-maroon-600 hover:text-white flex items-center"><i class="fas fa-calendar-check mr-2"></i>Reserve Now</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="relative">
                    <a href="userdashboard.php" id="account-toggle" class="p-2 rounded block text-white flex items-center">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture" class="hover:border-yellow-400 w-10 h-10 rounded-full border-2 border-gray">
                        <span class="ml-2 text-white hover:text-yellow-400"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <i class="fas fa-chevron-down ml-2"></i>
                    </a>
                    <ul id="account-dropdown" class="absolute bg-white text-black rounded shadow-lg mt-2 p-2 right-0 hidden z-50">
                        <li><a href="userdashboard.php" class="block px-4 py-2 hover:bg-gray-200">My Profile</a></li>
                        <li><a href="orders.php" class="block px-4 py-2 hover:bg-gray-200">My Reservation</a></li>
                        <li><a href="account_setting.php" class="block px-4 py-2 hover:bg-gray-200">Settings</a></li>
                        <li>
                            <hr class="border-gray-300">
                        </li>
                        <li><a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-200">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php" class="bg-maroon-600 text-white px-4 py-2 rounded-md hover:bg-maroon-700"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Mobile Menu -->
    <ul id="mobile-menu" class="menu-enter hidden lg:hidden flex flex-col space-y-4 bg-gray-100 p-4">
        <li><a href="index.php" class="text-gray-600 hover:text-gray-800"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="#menuu" class="text-gray-600 hover:text-gray-800"><i class="fas fa-utensils mr-2"></i></i> Menu</a></li>
        <li><a href="#promotions" class="text-gray-600 hover:text-gray-800"><i class="fas fa-concierge-bell"></i> Promo</a></li>
        <li><a href="#about" class="text-gray-600 hover:text-gray-800"><i class="fas fa-calendar-check"></i> About Us</a></li>
        <li><a href="#contact" class="text-gray-600 hover:text-gray-800"><i class="fas fa-phone-alt"></i> Contact</a></li>
        <li><a href="choose.php" class="text-white bg-maroon-500 hover:text-yellow-400 px-4 py-2 rounded-md hover:bg-maroon-600 hover:text-white flex items-center"><i class="fas fa-calendar-check mr-2"></i>Reserve Now</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="relative">

                <a href="userdashboard.php" id="mobile-account-toggle" class="hover:bg-maroon-700 flex p-2 rounded block">
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture" class="w-8 h-8 rounded-full">
                    <p class="ml-3"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                </a>
                <ul id="mobile-account-dropdown" class="bg-white text-black rounded shadow-lg mt-2 p-2">
                    <li><a href="userdashboard.php" class="block px-4 py-2 hover:bg-gray-200">My Profile</a></li>
                    <li>
                        <hr class="border-gray-300">
                    </li>
                    <li><a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-200">Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li><a href="login.php" class="hover:bg-maroon-700 p-2 rounded block"><i class="fas fa-sign-in-alt"></i> Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<script>
    // Toggle mobile menu
    document.getElementById('hamburger').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    });

    // Toggle account dropdown
    document.getElementById('account-toggle').addEventListener('click', function(event) {
        event.preventDefault();
        const accountDropdown = document.getElementById('account-dropdown');
        accountDropdown.classList.toggle('hidden');
    });

    // Toggle mobile account dropdown
    document.getElementById('mobile-account-toggle').addEventListener('click', function(event) {
        event.preventDefault();
        const mobileAccountDropdown = document.getElementById('mobile-account-dropdown');
        mobileAccountDropdown.classList.toggle('hidden');
    });
</script>

<style>
    .bg-maroon-900 {
        background-color: #800000;
    }

    .bg-maroon-700 {
        background-color: #a52a2a;
    }

    .bg-maroon-600 {
        background-color: #b22222;
    }

    .hover\:bg-maroon-700:hover {
        background-color: #a52a2a;
    }

    .hover\:bg-maroon-600:hover {
        background-color: #b22222;
    }
</style>