<main class="main-content p-6">
    <?php
    include 'conn.php';
    $user_id = $_SESSION['user_id'];
    $query = "
        SELECT 
            r.reservation_id, r.user_id, r.reservation_date, r.reservation_time, r.dine_in_or_takeout, 
            r.takeout_type, r.delivery_location, r.guests, r.payment_method, r.special_requests, 
            r.status, r.created_at, r.cancel_reason,
            u.name AS user_name, u.email AS user_email, u.phone,  
            GROUP_CONCAT(m.name SEPARATOR ', ') AS menu_items,
            GROUP_CONCAT(m.image_url SEPARATOR ', ') AS menu_images,
            GROUP_CONCAT(m.price SEPARATOR ', ') AS menu_prices,
            GROUP_CONCAT(rm.quantity SEPARATOR ', ') AS menu_quantities,
            GROUP_CONCAT(p.image_url SEPARATOR ', ') AS promo_images,
            GROUP_CONCAT(p.discounted_price SEPARATOR ', ') AS promo_prices
        FROM reservation r
        JOIN users u ON r.user_id = u.user_id
        LEFT JOIN reservation_menu rm ON r.reservation_id = rm.reservation_id
        LEFT JOIN menu m ON rm.menu_item_id = m.id
        LEFT JOIN promotions p ON rm.promo_item_id = p.id
        WHERE r.user_id = ?
        GROUP BY r.reservation_id
        ORDER BY r.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $done = "Completed";
    ?>
    <div class="reservations">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Finished Reservations</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($reservations as $reservation):
                if ($reservation['status'] !== 'Confirmed') {
                    continue;
                }

                // Check if the review exists for the reservation
                $reservation_id = $reservation['reservation_id'];
                $review_query = "SELECT COUNT(*) AS review_count FROM reviews WHERE reservation_id = ?";
                $stmt = $conn->prepare($review_query);
                $stmt->bind_param("i", $reservation_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $review_data = $result->fetch_assoc();
                $has_review = $review_data['review_count'] > 0;
                $stmt->close();

                $statusColors = [
                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'Confirmed' => 'bg-green-100 text-green-800 border-green-200',
                    'Cancelled' => 'bg-red-100 text-red-800 border-red-200',
                    'Completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'Pending Cancellation' => 'bg-orange-100 text-orange-800 border-orange-200'
                ];
                $statusColor = $statusColors[$reservation['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                ?>
                <div
                    class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-100">
                  

                    <div class="mt-2">
                        <a href="view_review.php?reservation_id=<?php echo $reservation['reservation_id']; ?>"
                            class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-100">
                            <span
                        class="absolute top-4 right-4 px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColor; ?> border">
                        <?php echo $done; ?>
                    </span>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Reservation
                                #<?php echo htmlspecialchars($reservation['reservation_id']); ?></h3>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="space-y-2">
                                    <p class="text-sm"><span class="font-medium text-gray-500">Date:</span> <span
                                            class="text-gray-900"><?php echo htmlspecialchars($reservation['reservation_date']); ?></span>
                                    </p>

                                </div>
                                <div class="space-y-2">
                                    <p class="text-sm"><span class="font-medium text-gray-500">Time:</span> <span
                                            class="text-gray-900"><?php echo htmlspecialchars($reservation['reservation_time']); ?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 mb-2">Selected Items:</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <?php
                                    $menu_items = explode(', ', $reservation['menu_items']);
                                    $menu_images = explode(', ', $reservation['menu_images']);
                                    $menu_prices = explode(', ', $reservation['menu_prices']);
                                    $menu_quantities = explode(', ', $reservation['menu_quantities']);

                                    foreach ($menu_items as $index => $item):
                                        if (!empty($menu_images[$index])):
                                            ?>
                                            <div class="relative group">
                                                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                                    <img src="uploads/<?php echo htmlspecialchars($menu_images[$index]); ?>"
                                                        alt="<?php echo htmlspecialchars($item); ?>"
                                                        class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-200">
                                                </div>
                                                <div class="mt-1 text-xs text-gray-600">
                                                    <p class="font-medium truncate"><?php echo htmlspecialchars($item); ?></p>
                                                    <p>₱<?php echo number_format(floatval($menu_prices[$index]), 2); ?> x
                                                        <?php echo $menu_quantities[$index]; ?></p>
                                                </div>
                                            </div>
                                            <?php
                                        endif;
                                    endforeach;
                                    if (!empty($reservation['promo_images'])):
                                        $promo_images = explode(', ', $reservation['promo_images']);
                                        $promo_prices = explode(', ', $reservation['promo_prices']);

                                        foreach ($promo_images as $index => $image):
                                            if (!empty($image)):
                                                ?>
                                                <div class="relative group">
                                                    <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                                        <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Promo Item"
                                                            class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-200">
                                                        <div
                                                            class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs rounded-bl">
                                                            PROMO</div>
                                                    </div>
                                                    <div class="mt-1 text-xs text-gray-600">
                                                        <p class="font-medium">Promo Item</p>
                                                        <p>₱<?php echo number_format(floatval($promo_prices[$index]), 2); ?></p>
                                                    </div>
                                                </div>
                                                <?php
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                            <?php
                            // Calculate total amount
                            $total = 0;
                            foreach ($menu_items as $index => $item) {
                                if (isset($menu_prices[$index]) && isset($menu_quantities[$index])) {
                                    $total += floatval($menu_prices[$index]) * intval($menu_quantities[$index]);
                                }
                            }
                            // Add promo items to total
                            if (!empty($reservation['promo_images'])) {
                                foreach ($promo_prices as $price) {
                                    $total += floatval($price);
                                }
                            }
                            ?>
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Total Amount:</span>
                                    <span
                                        class="text-lg font-bold text-gray-900">₱<?php echo number_format($total, 2); ?></span>
                                </div>
                            </div>
                        </a>
                        <!-- Review Form -->
                        <?php if (!$has_review): ?>
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <form action="submit_review.php" method="POST">
                                    <input type="hidden" name="reservation_id"
                                        value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                    <div class="mb-4">
                                        <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                                        <select name="rating" id="rating"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="1">1 - Very Poor</option>
                                            <option value="2">2 - Poor</option>
                                            <option value="3">3 - Average</option>
                                            <option value="4">4 - Good</option>
                                            <option value="5">5 - Excellent</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="review" class="block text-sm font-medium text-gray-700">Review</label>
                                        <textarea name="review" id="review" rows="4"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Submit Review
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <p class="mt-6 text-sm text-gray-500">You have already submitted a review for this reservation.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</main>