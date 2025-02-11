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
    $conn->close();
    ?>

    <div class="reservations p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">History</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($reservations as $reservation):
                if ($reservation['status'] !== 'Confirmed') {
                    continue;
                }
                $statusColors = [
                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'Confirmed' => 'bg-green-100 text-green-800 border-green-200',
                    'Cancelled' => 'bg-red-100 text-red-800 border-red-200',
                    'Completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'Pending Cancellation' => 'bg-orange-100 text-orange-800 border-orange-200'
                ];
                $statusColor = $statusColors[$reservation['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                ?>
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-100">
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColor; ?> border">
                        <?php echo htmlspecialchars('✔'); ?>
                    </span>
                    <div class="mt-2">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Reservation #<?php echo htmlspecialchars($reservation['reservation_id']); ?></h3>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium text-gray-500">Date:</span> <span class="text-gray-900"><?php echo htmlspecialchars($reservation['reservation_date']); ?></span></p>
                                <p class="text-sm"><span class="font-medium text-gray-500">Time:</span> <span class="text-gray-900"><?php echo htmlspecialchars($reservation['reservation_time']); ?></span></p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium text-gray-500">Type:</span> <span class="text-gray-900"><?php echo htmlspecialchars($reservation['dine_in_or_takeout']); ?></span></p>
                                <p class="text-sm"><span class="font-medium text-gray-500">Guests:</span> <span class="text-gray-900"><?php echo htmlspecialchars($reservation['guests']); ?></span></p>
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
                                                <img src="uploads/<?php echo htmlspecialchars($menu_images[$index]); ?>" alt="<?php echo htmlspecialchars($item); ?>" class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-200">
                                            </div>
                                            <div class="mt-1 text-xs text-gray-600">
                                                <p class="font-medium truncate"><?php echo htmlspecialchars($item); ?></p>
                                                <p>₱<?php echo number_format(floatval($menu_prices[$index]), 2); ?> x <?php echo $menu_quantities[$index]; ?></p>
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
                                                    <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Promo Item" class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-200">
                                                    <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs rounded-bl">PROMO</div>
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
                                <span class="text-lg font-bold text-gray-900">₱<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>

                        <?php if (!empty($reservation['special_requests'])): ?>
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500">Special Requests:</p>
                                <p class="text-sm text-gray-900 mt-1"><?php echo htmlspecialchars($reservation['special_requests']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
<?php
include 'conn.php';

$query = "SELECT 
    rc.id as reservation_id,
    rc.contract_date,
    rc.event_name,
    rc.company_name,
    rc.owner,
    rc.phone_number,
    rc.email,
    rc.bir_permit,
    rc.location,
    rc.event_start,
    rc.event_end,
    rc.guests,
    rc.cost,
    rc.services,
    rc.status,
    rc.payment_screenshot,
    rc.created_at,
    GROUP_CONCAT(cm.name SEPARATOR ', ') AS menu_items,
    GROUP_CONCAT(cm.image_url SEPARATOR ', ') AS menu_images,
    GROUP_CONCAT(rcm.quantity SEPARATOR ', ') AS menu_quantities
FROM reservation_catering rc
LEFT JOIN reservation_menu rcm ON rc.id = rcm.reservation_id
LEFT JOIN catering_menu cm ON rcm.menu_item_id = cm.id
WHERE rc.user_id = ?
GROUP BY rc.id
ORDER BY rc.created_at DESC";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC); // Fetch all reservations into an array
} else {
    die("Error preparing statement: " . $conn->error);
}

// Helper functions
function formatDate($dateString)
{
    if (!$dateString) return '';
    return date('F j, Y', strtotime($dateString));
}

function formatDateTime($dateTimeString)
{
    if (!$dateTimeString) return '';
    return date('F j, Y g:i A', strtotime($dateTimeString));
}

function formatNumber($number)
{
    return number_format($number, 2);
}

function getStatusClass($status)
{
    $statusClasses = [
        'confirmed' => 'bg-green-100 text-green-800 border-green-200',
        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
        'pending cancellation' => 'bg-orange-100 text-orange-800 border-orange-200'
    ];
    return $statusClasses[strtolower($status)] ?? 'bg-gray-100 text-gray-800 border-gray-200';
}
?>


        <h2 class="text-2xl font-semibold text-gray-800 mt-5 mb-4">Caterings History</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            if (!empty($reservations)) {
                foreach ($reservations as $reservation):
                    // Skip reservations that are not "Confirmed"
                    if ($reservation['status'] !== 'confirmed') {
                        continue;
                    }

                    $statusColor = getStatusClass($reservation['status']);
            ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-100">
                        <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColor; ?> border">
                            <?php echo htmlspecialchars(ucfirst($reservation['status'])); ?>
                        </span>
                        <div class="mt-2">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Event: <?php echo htmlspecialchars($reservation['event_name']); ?></h3>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="space-y-2">
                                    <p class="text-sm"><span class="font-medium text-gray-500">Date:</span> <span class="text-gray-900"><?php echo formatDate($reservation['contract_date']); ?></span></p>
                                    <p class="text-sm"><span class="font-medium text-gray-500">Company:</span> <span class="text-gray-900"><?php echo htmlspecialchars($reservation['company_name']); ?></span></p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-sm"><span class="font-medium text-gray-500">Location:</span> <span class="text-gray-900"><?php echo htmlspecialchars($reservation['location']); ?></span></p>
                                    <p class="text-sm"><span class="font-medium text-gray-500">Guests:</span> <span class="text-gray-900"><?php echo htmlspecialchars($reservation['guests']); ?></span></p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 mb-2">Selected Items:</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <?php
                                    $menu_items = explode(', ', $reservation['menu_items']);
                                    $menu_images = explode(', ', $reservation['menu_images']);
                                    $menu_quantities = explode(', ', $reservation['menu_quantities']);

                                    foreach ($menu_items as $index => $item):
                                        if (!empty($menu_images[$index])):
                                            ?>
                                            <div class="relative group">
                                                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                                    <img src="uploads/<?php echo htmlspecialchars($menu_images[$index]); ?>" alt="<?php echo htmlspecialchars($item); ?>" class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-200">
                                                </div>
                                                <div class="mt-1 text-xs text-gray-600">
                                                    <p class="font-medium truncate"><?php echo htmlspecialchars($item); ?></p>
                                                    <p>x<?php echo $menu_quantities[$index]; ?></p>
                                                </div>
                                            </div>
                                        <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Total Amount:</span>
                                    <span class="text-lg font-bold text-gray-900">₱<?php echo formatNumber($reservation['cost']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                endforeach;
            } else {
                echo '<div class="col-span-full text-center py-8 text-gray-500">No catering reservations found.</div>';
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>

    </div>
    
</main>