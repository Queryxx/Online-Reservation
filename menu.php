<?php include 'conn.php';
$result = $conn->query("
SELECT 
    id, 
    name, 
    price, 
    discounted_price, 
    image_url, 
    alt_text, 
    'Promotion' AS item_type, 
    'Promotions' AS category,
    NULL AS status 
FROM promotions 
UNION ALL 
SELECT 
    id, 
    name, 
    price, 
    NULL AS discounted_price, 
    image_url, 
    alt_text, 
    'Menu' AS item_type, 
    category,
    status 
FROM menu 
ORDER BY item_type DESC, category ASC, id ASC
");
$menu_items = $result->fetch_all(MYSQLI_ASSOC);
$conn->close(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .not-available {
            filter: grayscale(100%);
            opacity: 0.7;
            pointer-events: none;
            position: relative;
        }

        .not-available::after {
            content: 'Not Available';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: bold;
            z-index: 10;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Back Button -->
    <a href="index.php" class="absolute top-4 left-4 text-2xl text-gray-800 hover:text-red-600">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Menu Section -->
    <section id="menu" class="py-16 p-7 bg-gray-50">
        <div class="container mx-auto text-center">
            <h3 class="text-3xl font-bold text-gray-800">Our Full Menu</h3>
            <p class="text-gray-600 mt-4">Browse all our offerings, including promotions and regular menu items!</p>

            <!-- Tabs -->
            <div class="tabs mt-10">
                <?php
                // Get unique categories from the menu items
                $categories = array_unique(array_column($menu_items, 'category'));
                foreach ($categories as $category) : ?>
                    <button class="tablink bg-gray-300 text-gray-800 px-4 py-2 rounded" onclick="openTab(event, '<?php echo htmlspecialchars($category); ?>')">
                        <strong style="text-transform: uppercase;"><?php echo htmlspecialchars($category); ?></strong>
                    </button>

                <?php endforeach; ?>
            </div>

            <!-- Tab Content -->
            <?php foreach ($categories as $category) : ?>
                <div id="<?php echo htmlspecialchars($category); ?>" class="tabcontent mt-10" style="display:none">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($menu_items as $item) : ?>
                            <?php if ($item['category'] === $category) : ?>
                                <?php $isNotAvailable = $item['status'] === 'not_available'; ?>
                                <div class="bg-white shadow-lg rounded-lg p-6 <?php echo $isNotAvailable ? 'not-available' : ''; ?>">
                                    <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>"
                                        alt="<?php echo htmlspecialchars($item['alt_text']); ?>"
                                        class="rounded-md w-full h-40 object-cover">
                                    <h4 class="text-xl font-bold mt-4"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p class="text-gray-600 mt-2">
                                        <?php if ($item['discounted_price']) : ?>
                                            <span class="text-red-500 line-through">₱<?php echo number_format($item['price'], 2); ?></span>
                                            <span class="text-green-600 font-bold ml-2">₱<?php echo number_format($item['discounted_price'], 2); ?></span>
                                        <?php else : ?>
                                            ₱<?php echo number_format($item['price'], 2); ?>
                                        <?php endif; ?>
                                    </p>
                                    <a href="reserveform.php">
                                        <button class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                                            <?php echo $isNotAvailable ? 'disabled' : ''; ?>>
                                            <i class="fas fa-calendar-check mr-2"></i>Reserve Now
                                        </button>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SwiperJS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        // Tab functionality
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" bg-red-600 text-white", " bg-gray-300 text-gray-800");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " bg-red-600 text-white";
        }

        // Set default tab
        document.getElementsByClassName("tablink")[0].click();
    </script>
</body>

</html>