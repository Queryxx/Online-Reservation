<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
include 'conn.php';

// Fetch all menu items from both promotions and menu tables
$result = $conn->query("
    SELECT id, name, price, discounted_price, image_url, alt_text, 'Promotion' AS item_type 
    FROM promotions
    UNION ALL
    SELECT id, name, price, NULL AS discounted_price, image_url, alt_text, 'Menu' AS item_type 
    FROM menu
    ORDER BY item_type DESC, id ASC
");
$menu_items = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="max-w-5xl mx-auto px-6 py-16">
        <div class="text-center bg-white shadow-md rounded-lg p-8">
            <h2 class="text-3xl font-semibold text-gray-800">Your Reservation is Being Processed</h2>
            <p class="mt-4 text-lg text-gray-600">Your reservation has been successfully submitted. Please wait for the approval by the admin.</p>
            <p class="mt-6 text-lg text-gray-600">You can go back to your account for more details.</p>
            
            <div class="mt-8">
                <a href="userdashboard.php" class="px-6 py-3 bg-red-600 hover:bg-red-500 text-white rounded-md font-semibold">
                <i class="fas fa-user mr-2"></i> Go Back to Your Account
                </a>
            </div>
        </div>
    </div>

    <!-- Menu Section -->
    <section id="menu" class="py-16 p-8 bg-gray-50">
        <div class="container mx-auto text-center">
            <h3 class="text-3xl font-bold text-gray-800">Victoria's Menu</h3>
            <p class="text-gray-600 mt-4">Browse all our offerings, including promotions and regular menu items!</p>

            <!-- Swiper -->
            <div class="swiper mySwiper mt-10">
                <div class="swiper-wrapper">
                    <?php foreach ($menu_items as $item): ?>
                        <div class="swiper-slide bg-white shadow-lg rounded-lg p-6">
                            <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>"
                                 alt="<?php echo htmlspecialchars($item['alt_text']); ?>"
                                 class="rounded-md w-full h-40 object-cover">
                            <h4 class="text-xl font-bold mt-4"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p class="text-gray-600 mt-2">
                                <?php if ($item['item_type'] === 'Promotion' && $item['discounted_price']): ?>
                                    <span class="text-red-500 line-through">₱<?php echo number_format($item['price'], 2); ?></span>
                                    <span class="text-green-600 font-bold ml-2">₱<?php echo number_format($item['discounted_price'], 2); ?></span>
                                <?php else: ?>
                                    ₱<?php echo number_format($item['price'], 2); ?>
                                <?php endif; ?>
                            </p>
                            <a href="reserveform.php">
                                <button class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                    <i class="fas fa-calendar-check mr-2"></i>Reserve Now
                                </button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
                <!-- Add Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- SwiperJS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper
        const swiper = new Swiper('.mySwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>

</body>
</html>