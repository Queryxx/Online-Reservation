<?php
include 'conn.php';

// Fetch the current content
$query = "SELECT * FROM about_us WHERE id = 1";
$result = $conn->query($query);
$about = $result->fetch_assoc();
$conn->close();
?>
<!-- About Us Section -->
<section id="about" class="py-16 bg-gray-100">
    <div class="container mx-auto px-6 lg:px-16">
        <div class="text-center mb-10">
            <h3 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars_decode($about['title']); ?></h3>
            <p class="text-gray-600 mt-4">
                <?php echo htmlspecialchars_decode($about['subtitle']); ?>
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <!-- About Us Image -->
            <div>
                <img src="uploads/<?php echo htmlspecialchars($about['image_url']); ?>" alt="Victoria Grill Restaurant"
                    class="rounded-lg shadow-lg">
            </div>
            <!-- About Us Content -->
            <div class="text-gray-800">
                <h4 class="text-2xl font-bold mb-4">Our Mission</h4>
                <p class="mb-4">
                    <?php echo nl2br(htmlspecialchars_decode($about['story'])); ?>
                </p>
                <h4 class="text-2xl font-bold mb-4">Our Vision</h4>
                <p>
                    <?php echo nl2br(htmlspecialchars_decode($about['commitment'])); ?>
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Location Section -->
<section id="location" class="py-16 bg-white">
    <div class="container mx-auto px-6 lg:px-16 text-center">
        <h3 class="text-3xl font-bold text-gray-800">Our Location</h3>
        <p class="text-gray-600 mt-4">Visit us at our convenient location.</p>
        <div class="mt-8">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3803.195233005102!2d120.61519897493962!3d17.593461683329256!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x338e5dd2438215d7%3A0x9c8e670048fe6712!2sVictoria%E2%80%99s%20Grill%20and%20Restaurant!5e0!3m2!1sen!2sph!4v1735895655133!5m2!1sen!2sph"
                width="100%" height="450" style="border:5px double maroon;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>
<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const menuLink = document.querySelector('a[href="#about"]');
        const menuSection = document.getElementById('about');

        menuLink.addEventListener('click', function(event) {
            event.preventDefault();
            menuSection.classList.add('visible');
            menuSection.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>