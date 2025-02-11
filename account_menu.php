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
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>

<body class="bg-gray-100">

    <!-- Back Button -->
    <a href="userdashboard.php" class="absolute top-8 left-8 text-2xl text-gray-800 hover:text-red-600">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Menu Section -->
    <section id="menu" class="py-16 bg-gray-50 p-9">
        <div class="container mx-auto text-center">
            <h3 class="text-3xl font-bold text-gray-800">Victoria's Menu</h3>
            <p class="text-gray-600 mt-4">Browse all our offerings, including promotions and regular menu items!</p>

            <!-- Menu Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
                <?php foreach ($menu_items as $item): ?>
                    <div class="bg-white shadow-lg rounded-lg p-6">
                    <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($item['alt_text']); ?>"
                                class="rounded-md w-full h-40 object-cover">
                        <h4 class="text-xl font-bold mt-4"><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p class="text-gray-600 mt-2">
                            <?php if ($item['item_type'] === 'Promotion' && $item['discounted_price']): ?>
                                <span class="text-red-500 line-through">₱<?php echo number_format($item['price'], 2); ?></span>
                                <span
                                    class="text-green-600 font-bold ml-2">₱<?php echo number_format($item['discounted_price'], 2); ?></span>
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
        </div>
    </section>

    <!-- SwiperJS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper (if needed for additional features like carousels)
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