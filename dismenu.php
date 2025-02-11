<?php
include 'conn.php';

// Fetch menu items from both promotions and menu tables
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
<!-- Menu Section -->
<section id="menuu" class="py-16 bg-gray-50">
    <div class="container mx-auto text-center">
        <h3 class="text-3xl font-bold text-gray-800">Our Menu</h3>
        <p class="text-gray-600 mt-4">Explore our delicious offerings made with the freshest ingredients.</p>

        <!-- Carousel -->
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
                                <span
                                    class="text-red-500 line-through">₱<?php echo number_format($item['price'], 2); ?></span>
                                <span
                                    class="text-green-600 font-bold ml-2">₱<?php echo number_format($item['discounted_price'], 2); ?></span>
                            <?php else: ?>
                                ₱<?php echo number_format($item['price'], 2); ?>
                            <?php endif; ?>
                        </p>
                        <button onclick="handleReserveClick()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        <i class="fas fa-calendar-check mr-2"></i>Reserve Now
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Swiper navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            <!-- Swiper pagination -->
            <div class="swiper-pagination"></div>
        </div>

        <!-- See All Button -->
        <a href="menu.php"
            class="inline-block mt-8 px-20 py-3 bg-gray-800 text-white font-bold rounded hover:bg-gray-900 transition">
            <i class="fas fa-utensils mr-2"></i>See All Menu
        </a>
    </div>
</section>

<!-- SwiperJS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="script/swipe.js"></script>

<script>
    function handleReserveClick() {
        <?php if (isset($_SESSION['user_id'])): ?>
            window.location.href = 'reserveform.php';
        <?php else: ?>
            window.location.href = 'login.php';
        <?php endif; ?>
    }
    document.addEventListener('DOMContentLoaded', function() {
        const menuLink = document.querySelector('a[href="#menuu"]');
        const menuSection = document.getElementById('menuu');

        menuLink.addEventListener('click', function(event) {
            event.preventDefault();
            menuSection.classList.add('visible');
            menuSection.scrollIntoView({ behavior: 'smooth' });
        });
    });
    
</script>