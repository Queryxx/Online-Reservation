<?php
include 'conn.php';

// Fetch promotions from the promotions table, including the valid_until field
$result = $conn->query("SELECT * FROM promotions ORDER BY valid_until DESC");
$promotions = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!-- Promotions Section -->
<section id="promotions" class="py-16 bg-white">
    <div class="container mx-auto text-center">
        <h3 class="text-3xl font-bold text-gray-800">Current Promo</h3>
        <p class="text-gray-600 mt-4">Check out our exclusive offers and limited-time deals!</p>

        <!-- Manual Carousel -->
        <div class="relative mt-10">
            <div class="overflow-hidden relative">
                <div id="carouselInner" class="flex transition-transform duration-500" style="transform: translateX(0);">
                    <?php foreach ($promotions as $promotion): ?>
                        <div class="min-w-full px-4">
                            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                                <!-- Display the Image -->
                                <img src="uploads/<?php echo htmlspecialchars($promotion['image_url']); ?>" 
                                    alt="<?php echo htmlspecialchars($promotion['alt_text']); ?>" 
                                    class="w-full h-80 object-cover">

                                <!-- Details Section -->
                                <div class="p-6">
                                    <!-- Promotion Title -->
                                    <h4 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($promotion['name']); ?></h4>
                                    <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($promotion['details']); ?></p>

                                    <!-- Valid Until Date -->
                                    <p class="text-sm text-gray-500 mt-2">
                                        <?php
                                        $valid_until = new DateTime($promotion['valid_until']);
                                        echo 'Valid until: ' . $valid_until->format('F j, Y');
                                        ?>
                                    </p>

                                    <!-- Discounted and Original Prices -->
                                    <p class="mt-4">
                                        <span class="line-through text-gray-400">₱<?php echo number_format($promotion['price'], 2); ?></span>
                                        <span class="text-red-600 font-bold ml-2">₱<?php echo number_format($promotion['discounted_price'], 2); ?></span>
                                    </p>

                                    <!-- Reserve Button -->
                                    <button onclick="handleReserveClick()" class="bg-red-600 text-white py-2 px-6 rounded mt-4 hover:bg-red-700">
                                    <i class="fas fa-calendar-check mr-2"></i>Reserve Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <button id="prevButton" 
                class="absolute top-1/2 left-0 transform -translate-y-1/2 text-gray-500 px-6 py-6 rounded-full hover:text-gray-700 transition">
                <i class="fas fa-chevron-left text-4xl"></i>
            </button>
            <button id="nextButton" 
                class="absolute top-1/2 right-0 transform -translate-y-1/2 text-gray-500 px-6 py-6 rounded-full hover:text-gray-700 transition">
                <i class="fas fa-chevron-right text-4xl"></i>
            </button>
        </div>

        <!-- See All Button -->
        <a href="call_promotion.php"
            class="inline-block mt-8 px-20 py-3 bg-gray-800 text-white font-bold rounded hover:bg-gray-900 transition">
            <i class="fas fa-tag mr-2"></i>See All Promos
        </a>
    </div>
</section>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    function handleReserveClick() {
        <?php if (isset($_SESSION['user_id'])): ?>
            window.location.href = 'reserveform.php';
        <?php else: ?>
            window.location.href = 'login.php';
        <?php endif; ?>
    }

    // Manual Carousel Implementation
    const carouselInner = document.getElementById('carouselInner');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const totalSlides = document.querySelectorAll('#carouselInner > div').length;
    let currentSlide = 0;

    function updateCarousel() {
        const offset = -currentSlide * 100;
        carouselInner.style.transform = `translateX(${offset}%)`;
    }

    prevButton.addEventListener('click', () => {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateCarousel();
    });

    nextButton.addEventListener('click', () => {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateCarousel();
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const menuLink = document.querySelector('a[href="#promotions"]');
        const menuSection = document.getElementById('promotions');

        menuLink.addEventListener('click', function(event) {
            event.preventDefault();
            menuSection.classList.add('visible');
            menuSection.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>

<style>
    #prevButton, #nextButton {
        background-color: transparent; /* Remove background color */
    }
    #prevButton:hover, #nextButton:hover {
        background-color: transparent; /* Ensure hover also has no background */
    }
    .shadow-lg:hover {
        transform: translateY(-4px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    }
</style>
