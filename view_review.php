<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];

    // Fetch the review for the given reservation ID
    $review_query = "SELECT rating, review, created_at FROM reviews WHERE reservation_id = ?";
    $stmt = $conn->prepare($review_query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $review_result = $stmt->get_result();
    $review = $review_result->fetch_assoc();

    // Fetch the reservation details
    $reservation_query = "
        SELECT 
            r.reservation_date, r.reservation_time, 
            GROUP_CONCAT(m.name SEPARATOR ', ') AS menu_items,
            GROUP_CONCAT(m.image_url SEPARATOR ', ') AS menu_images,
            GROUP_CONCAT(m.price SEPARATOR ', ') AS menu_prices,
            GROUP_CONCAT(rm.quantity SEPARATOR ', ') AS menu_quantities
        FROM reservation r
        LEFT JOIN reservation_menu rm ON r.reservation_id = rm.reservation_id
        LEFT JOIN menu m ON rm.menu_item_id = m.id
        WHERE r.reservation_id = ?
        GROUP BY r.reservation_id
    ";
    $stmt = $conn->prepare($reservation_query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $reservation_result = $stmt->get_result();
    $reservation = $reservation_result->fetch_assoc();

    // Close the statement
    $stmt->close();
    if (!$review || !$reservation) {
        // JavaScript alert and redirect
        echo "<script>
            alert('No review or reservation found for this ID.');
            window.location.href = 'reviews.php';
        </script>";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Review</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6 mt-10">
        <div class="flex justify-between items-center mb-6">
            <a href="reviews.php" class="text-indigo-600 hover:text-indigo-900 text-2xl p-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-center flex-grow">Review for Reservation
                #<?php echo htmlspecialchars($reservation_id); ?></h1>
        </div>
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-semibold mb-6">Order Details</h2>
                    <p class="mb-4"><span class="font-medium text-gray-500">Date:</span>
                        <?php echo htmlspecialchars($reservation['reservation_date']); ?></p>
                    <p class="mb-4"><span class="font-medium text-gray-500">Time:</span>
                        <?php echo htmlspecialchars($reservation['reservation_time']); ?></p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                        <?php
                        $menu_items = explode(', ', $reservation['menu_items']);
                        $menu_images = explode(', ', $reservation['menu_images']);
                        $menu_prices = explode(', ', $reservation['menu_prices']);
                        $menu_quantities = explode(', ', $reservation['menu_quantities']);

                        foreach ($menu_items as $index => $item) {
                            if (!empty($menu_images[$index])) {
                                ?>
                                <div class="relative group bg-white p-4 rounded-lg shadow-md border border-gray-300">
                                    <div class="aspect-w-4 aspect-h-3 rounded-lg overflow-hidden bg-gray-100">
                                        <img src="uploads/<?php echo htmlspecialchars($menu_images[$index]); ?>"
                                            alt="<?php echo htmlspecialchars($item); ?>"
                                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-200">
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p class="font-medium truncate"><?php echo htmlspecialchars($item); ?></p>
                                        <p>₱<?php echo number_format(floatval($menu_prices[$index]), 2); ?> x
                                            <?php echo $menu_quantities[$index]; ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Review</h2>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-500">Rating:</span>
                        <div class="text-lg font-bold text-gray-900">
                            <?php
                            $rating = intval($review['rating']);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star text-yellow-500"></i>';
                                } else {
                                    echo '<i class="far fa-star text-yellow-500"></i>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-500">Comment:</span>
                        <p class="text-gray-900"><?php echo htmlspecialchars($review['review']); ?></p>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-500">Submitted on:</span>
                        <span class="text-gray-900"><?php echo htmlspecialchars($review['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>