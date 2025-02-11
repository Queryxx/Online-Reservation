<?php
session_start();
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
        WHERE r.user_id = ? AND r.status = 'Pending'
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

// Calculate total amount for each reservation
foreach ($reservations as &$reservation) {
    $menu_items = explode(', ', $reservation['menu_items']);
    $menu_prices = explode(', ', $reservation['menu_prices']);
    $menu_quantities = explode(', ', $reservation['menu_quantities']);
    $promo_prices = explode(', ', $reservation['promo_prices']);

    $total = 0;
    foreach ($menu_items as $index => $item) {
        if (isset($menu_prices[$index]) && isset($menu_quantities[$index])) {
            $total += floatval($menu_prices[$index]) * intval($menu_quantities[$index]);
        }
    }

    if (!empty($reservation['promo_images'])) {
        foreach ($promo_prices as $price) {
            $total += floatval($price);
        }
    }

    $reservation['total'] = $total;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-200 text-white">

    <main class="main-content p-6 min-h-screen">
        <!-- put history icon button here -->
        <div class="reservations max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <a href="userdashboard.php" class="text-white hover:text-gray-300">
                    <i class="fas fa-arrow-left text-2xl"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($reservations)): ?>
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center">
                        <p class="text-xl font-semibold">No Reservations</p>
                    </div>
                <?php else: ?>
                    <?php
                    $statusColors = [
                        'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'Confirmed' => 'bg-green-100 text-green-800 border-green-200',
                        'Cancelled' => 'bg-red-100 text-red-800 border-red-200',
                        'Completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'Pending Cancellation' => 'bg-orange-100 text-orange-800 border-orange-200'
                    ];
                    ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <?php $statusColor = $statusColors[$reservation['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200'; ?>
                        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-200 cursor-pointer text-gray-900"
                            onclick="openReservationModal(<?php echo htmlspecialchars($reservation['reservation_id']); ?>)">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-lg font-bold">Reservation ID:
                                    <?php echo htmlspecialchars($reservation['reservation_id']); ?>
                                </p>
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded <?php echo $statusColor; ?>"><?php echo htmlspecialchars('Active Reservation'); ?></span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <p class="text-sm"><strong>Date:</strong>
                                    <?php echo htmlspecialchars($reservation['reservation_date']); ?></p>
                                <p class="text-sm"><strong>Time:</strong>
                                    <?php echo htmlspecialchars($reservation['reservation_time']); ?></p>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <p class="text-lg font-bold"><strong>Total Amount:</strong>
                                    ₱<?php echo number_format($reservation['total'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Structure -->
    <div id="uniqueReservationModal"
        class="fixed inset-0 bg-gray-800 mt-5 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2 text-gray-900">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Reservation Details</h2>
                <button onclick="closeReservationModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div id="modalContent">
                <!-- Modal content will be loaded here via JavaScript -->
            </div>
        </div>
    </div>
    <style>
        .scrollable-content {
            max-height: 70vh;
            /* Adjust the height as needed */
            overflow-y: auto;
        }
    </style>

    <script>
        function openReservationModal(reservationId) {
            // Fetch reservation details using AJAX or populate from existing data
            // For simplicity, assuming data is available in a JavaScript object
            const reservation = <?php echo json_encode($reservations); ?>.find(r => r.reservation_id == reservationId);

            // Populate modal content
            const modalContent = `
            <div class="mt-2 scrollable-content">
                <h3 class="text-lg font-bold mb-4">Reservation #${reservation.reservation_id}</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="space-y-2">
                        <p class="text-sm"><span class="font-medium text-gray-500">Date:</span> <span class="text-gray-900">${reservation.reservation_date}</span></p>
                        <p class="text-sm"><span class="font-medium text-gray-500">Time:</span> <span class="text-gray-900">${reservation.reservation_time}</span></p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm"><span class="font-medium text-gray-500">Type:</span> <span class="text-gray-900">${reservation.dine_in_or_takeout}</span></p>
                        <p class="text-sm"><span class="font-medium text-gray-500">Guests:</span> <span class="text-gray-900">${reservation.guests}</span></p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-500 mb-2">Selected Items:</p>
                    <div class="grid grid-cols-3 gap-2">
                        ${reservation.menu_items.split(', ').map((item, index) => `
                            <div class="relative group">
                                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                    <img src="uploads/${reservation.menu_images.split(', ')[index]}" alt="${item}" class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-200">
                                </div>
                                <div class="mt-1 text-xs text-gray-600">
                                    <p class="font-medium truncate">${item}</p>
                                    <p>₱${parseFloat(reservation.menu_prices.split(', ')[index]).toFixed(2)} x ${reservation.menu_quantities.split(', ')[index]}</p>
                                </div>
                            </div>
                        `).join('')}
                        ${reservation.promo_images ? reservation.promo_images.split(', ').map((image, index) => `
                            <div class="relative group">
                                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                    <img src="uploads/${image}" alt="Promo Item" class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-200">
                                    <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs rounded-bl">PROMO</div>
                                </div>
                                <div class="mt-1 text-xs text-gray-600">
                                    <p class="font-medium">Promo Item</p>
                                    <p>₱${parseFloat(reservation.promo_prices.split(', ')[index]).toFixed(2)}</p>
                                </div>
                            </div>
                        `).join('') : ''}
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Total Amount:</span>
                        <span class="text-lg font-bold text-gray-900">₱${parseFloat(reservation.total).toFixed(2)}</span>
                    </div>
                </div>
                ${reservation.special_requests ? `
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Special Requests:</p>
                        <p class="text-sm text-gray-900 mt-1">${reservation.special_requests}</p>
                    </div>
                ` : ''}
            </div>
        `;
            document.getElementById('modalContent').innerHTML = modalContent;

            // Show modal
            document.getElementById('uniqueReservationModal').classList.remove('hidden');
        }

        function closeReservationModal() {
            document.getElementById('uniqueReservationModal').classList.add('hidden');
        }
    </script>
</body>

</html>