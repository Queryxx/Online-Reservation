<?php
include 'conn.php';

// Fetch all promotions from the promotions table
$result = $conn->query("SELECT * FROM promotions ORDER BY valid_until DESC");
$promotions = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">

    <!-- Back Button -->
    <a href="index.php" class="absolute top-4 left-4 text-2xl text-gray-800 hover:text-red-600">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Promotions Section -->
    <section id="promotions" class="py-16 p-4 bg-gray-50">
        <div class="container mx-auto text-center">
            <h3 class="text-3xl font-bold text-gray-800">All Promos</h3>
            <p class="text-gray-600 mt-4">Browse all our exclusive offers and limited-time deals!</p>

            <!-- Promotions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
                <?php foreach ($promotions as $promotion): ?>
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <img src="uploads/<?php echo htmlspecialchars($promotion['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($promotion['alt_text']); ?>"
                            class="rounded-md w-full h-40 object-cover">
                        <h4 class="text-xl font-bold mt-4"><?php echo htmlspecialchars($promotion['name']); ?></h4>
                        <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($promotion['details']); ?></p>
                        <p class="mt-4 text-gray-800 font-semibold">
                            Valid until:
                            <?php
                            $valid_until = new DateTime($promotion['valid_until']);
                            echo $valid_until->format('F j, Y');
                            ?>
                        </p>
                        <p class="mt-4 text-gray-800">
                            <span
                                class="line-through text-gray-500">₱<?php echo number_format($promotion['price'], 2); ?></span>
                            <span
                                class="text-red-600 font-bold ml-2">₱<?php echo number_format($promotion['discounted_price'], 2); ?></span>
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

</body>

</html>