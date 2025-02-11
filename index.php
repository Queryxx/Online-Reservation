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

</head>

<body class="bg-gray-100">
   <!-- Navigation -->
<?php include 'nav.php' ?>
<?php
include 'conn.php';

// Fetch hero section content from the database
$stmt = $conn->prepare("SELECT title, subtitle, background_image FROM hero_section WHERE id = 1");
if ($stmt) {
    $stmt->execute();
    $stmt->bind_result($title, $subtitle, $background_image);
    $stmt->fetch();
    $stmt->close();
} else {
    // Handle error
    $title = "Welcome to Victoria Grill";
    $subtitle = "Savor the flavors, relish the experience.";
    $background_image = "Victoria.jpg";
}
$conn->close();

// Use default image if no image is set
if (empty($background_image)) {
    $background_image = "Victoria.jpg";
}
?>
<style>
    /* Fade-in animation */
    .fade-in {
        opacity: 0;
        transition: opacity 1s ease-out;
    }

    .fade-in.visible {
        opacity: 1;
    }

    /* Slide-in from right animation */
    .slide-in {
        opacity: 0;
        transform: translateX(20px);
        transition: opacity 1s ease-out, transform 1s ease-out;
    }

    .slide-in.visible {
        opacity: 1;
        transform: translateX(0);
    }
</style>

<!-- Hero Section -->
<header class="bg-cover bg-center h-screen fade-in" style="background-image: url('uploads/<?php echo htmlspecialchars($background_image); ?>');">
    <div class="bg-black bg-opacity-50 h-full flex flex-col items-center justify-center text-center text-white">
        <h2 class="text-4xl md:text-6xl font-bold slide-in"><?php echo htmlspecialchars($title); ?></h2>
        <p class="text-lg md:text-2xl mt-4 slide-in"><?php echo htmlspecialchars($subtitle); ?></p>
        <a href="choose.php" class="mt-8 bg-maroon-500 hover:bg-maroon-600 text-white px-6 py-3 rounded-lg text-lg flex items-center slide-in">
            <i class="fas fa-calendar-check mr-2"></i>Reserve Now
        </a>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fadeInElements = document.querySelectorAll('.fade-in');
        const slideInElements = document.querySelectorAll('.slide-in');

        fadeInElements.forEach(element => {
            element.classList.add('visible');
        });

        slideInElements.forEach(element => {
            element.classList.add('visible');
        });
    });
</script>
    <!-- Menu Section -->
    <?php include 'dismenu.php'; ?>
    <!-- Promotion Section -->
    <?php include 'promotion.php'; ?>
    <!-- About Section -->
    <?php include 'about.php'; ?>
    <!-- Contact Section -->
    <?php include 'contact.php'; ?>
    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <style>
        .bg-maroon-500 {
            background-color: #800000;
        }

        .hover\:bg-maroon-600:hover {
            background-color: #b22222;
        }
    </style>
</body>

</html>