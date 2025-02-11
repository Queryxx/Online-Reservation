<?php
include 'admin/submit_contact.php'; // Include the PHP script
?>
<!-- Contact Section -->
<section id="contact" class="py-16 bg-white">
    <div class="container mx-auto px-6 lg:px-16 text-center">
        <h3 class="text-3xl font-bold text-gray-800">Contact Us</h3>
        <p class="text-gray-600 mt-4">Have questions or special requests? We’d love to hear from you!</p>
        <?php echo $messageStatus; ?> <!-- Display success/error message -->
        <form class="mt-8" action="" method="POST" onsubmit="return checkLoginStatus()">
            <textarea name="message" placeholder="Your Message"
                class="mt-4 p-4 border rounded-md w-full focus:outline-none focus:ring-2 focus:ring-red-500"
                required></textarea>
            <button type="submit" class="mt-4 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-paper-plane mr-2"></i>Send Message
            </button>
        </form>
    </div>
</section>

<script>
    function checkLoginStatus() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('You must be logged in to send a message.');
            window.location.href = 'login.php';
            return false;
        <?php endif; ?>
        return true;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const menuLink = document.querySelector('a[href="#contact"]');
        const menuSection = document.getElementById('contact');

        menuLink.addEventListener('click', function(event) {
            event.preventDefault();
            menuSection.classList.add('visible');
            menuSection.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>