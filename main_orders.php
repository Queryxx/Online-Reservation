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
<main class="main-content p-6 bg-gray-100 min-h-screen">
    <!-- put history icon button here -->
    <div class="reservations max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Your Reservations</h2>
            <a href="history.php" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-history text-2xl"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-200 cursor-pointer"
                    onclick="openReservationModal(<?php echo htmlspecialchars($reservation['reservation_id']); ?>)">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-lg font-bold text-gray-700">Reservation ID:
                            <?php echo htmlspecialchars($reservation['reservation_id']); ?>
                        </p>
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded <?php echo $statusColor; ?>"><?php echo htmlspecialchars($reservation['status']); ?></span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <p class="text-sm text-gray-600"><strong>Date:</strong>
                            <?php echo htmlspecialchars($reservation['reservation_date']); ?></p>
                        <p class="text-sm text-gray-600"><strong>Time:</strong>
                            <?php echo htmlspecialchars($reservation['reservation_time']); ?></p>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-lg font-bold text-gray-900"><strong>Total Amount:</strong>
                            ₱<?php echo number_format($reservation['total'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<!-- Modal Structure -->
<div id="uniqueReservationModal"
    class="fixed inset-0 bg-gray-800 mt-5 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2">
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
                <h3 class="text-lg font-bold text-gray-900 mb-4">Reservation #${reservation.reservation_id}</h3>
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
               ${reservation.status === 'Pending' ? `
    <div class="mt-6 flex gap-2">
        <button onclick="openCancelModal(${reservation.reservation_id})" class="w-full bg-red-50 text-red-600 hover:bg-red-100 py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
            <i class="fas fa-times-circle"></i> Cancel Reservation
        </button>
        <button onclick="openRescheduleModal(${reservation.reservation_id})" class="w-full bg-yellow-50 text-yellow-600 hover:bg-yellow-100 py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
            <i class="fas fa-calendar-alt"></i> Reschedule
        </button>
    </div>
` : reservation.status === 'Pending Cancellation' ? `
    <div class="mt-6">
        <a href="cancel_reversal.php?reservation_id=${reservation.reservation_id}" class="w-full bg-blue-50 text-blue-600 hover:bg-blue-100 py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 no-underline">
            <i class="fas fa-undo"></i> Cancel Cancellation Request
        </a>
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

    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('new_date');
        const timeInput = document.getElementById('new_time');

        // Set the minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        // Function to update the minimum time
        function updateMinTime() {
            const selectedDate = new Date(dateInput.value);
            const now = new Date();
            const minTime = new Date(now.getTime() + 20 * 60000); // 20 minutes from now

            if (selectedDate.toDateString() === now.toDateString()) {
                const hours = minTime.getHours().toString().padStart(2, '0');
                const minutes = minTime.getMinutes().toString().padStart(2, '0');
                timeInput.setAttribute('min', `${hours}:${minutes}`);
            } else {
                timeInput.removeAttribute('min');
            }
        }

        // Update the minimum time when the date changes
        dateInput.addEventListener('change', updateMinTime);

        // Update the minimum time on page load
        updateMinTime();
    });
</script>


<!-- Reschedule Modal Structure -->
<div id="rescheduleModal" class="fixed inset-0 bg-gray-800 mt-5 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Reschedule Reservation</h2>
            <button onclick="closeRescheduleModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form id="rescheduleForm" method="POST" action="reschedule_reservation.php" onsubmit="return validateRescheduleForm()">
    <input type="hidden" name="reservation_id" id="rescheduleReservationId">
    <div class="mb-4">
        <label for="new_date" class="block text-sm font-medium text-gray-700">New Date</label>
        <input type="date" name="new_date" id="new_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    </div>
    <div class="mb-4">
        <label for="new_time" class="block text-sm font-medium text-gray-700">New Time</label>
        <input type="time" name="new_time" id="new_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors duration-200">Reschedule</button>
    </div>
</form>
</div>
</div>

<script>
    function openRescheduleModal(reservationId) {
        document.getElementById('rescheduleReservationId').value = reservationId;
        document.getElementById('rescheduleModal').classList.remove('hidden');
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').classList.add('hidden');
    }

    function validateRescheduleForm() {
        const newDate = document.getElementById('new_date').value;
        const newTime = document.getElementById('new_time').value;
        const currentDate = new Date();
        const selectedDate = new Date(newDate + 'T' + newTime);

        if (selectedDate < currentDate) {
            alert('The selected date and time cannot be in the past.');
            return false;
        }
        return true;
    }
</script>