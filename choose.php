<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Victoria Grill Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .bg-maroon-500 {
            background-color: #800000;
        }

        .hover\:bg-maroon-600:hover {
            background-color: #b22222;
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
 

    <?php include 'nav.php' ?>

    <!-- Menu Section -->
    <section id="menu" class="py-16 p-5 bg-gray-50 flex-grow">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold mb-8 text-gray-800">Choose an Option</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">
                <!-- Catering Card -->
                <a href="catering.php" class="group bg-white rounded-2xl shadow-md p-6 flex flex-col items-center border border-gray-200 hover:shadow-lg hover:border-maroon-500 transition duration-300">
                    <div class="bg-maroon-500 text-white p-6 rounded-full shadow-lg group-hover:bg-maroon-600 transition">
                    <i class="fas fa-concierge-bell text-4xl"></i>   
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold text-gray-800 group-hover:text-maroon-500 transition">Catering</h2>
                    <p class="text-gray-600 mt-2 text-sm">Order delicious food for your events.</p>
                </a>
                
                <!-- Meal Reservation Card -->
                <a href="reserveform.php" class="group bg-white rounded-2xl shadow-md p-6 flex flex-col items-center border border-gray-200 hover:shadow-lg hover:border-maroon-500 transition duration-300">
                    <div class="bg-maroon-500 text-white p-6 rounded-full shadow-lg group-hover:bg-maroon-600 transition">
                    <i class="fas fa-utensils text-4xl"></i>
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold text-gray-800 group-hover:text-maroon-500 transition">Reserve a Meal</h2>
                    <p class="text-gray-600 mt-2 text-sm">Book a table and enjoy our finest dishes.</p>
                </a>
            </div>
        </div>
    </section><footer class="bg-gray-800 text-gray-300 mt-auto">
        <?php include 'footer.php' ?>
    </footer>
</body>

</html>
