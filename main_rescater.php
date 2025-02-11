<?php
session_start();
include 'conn.php';

$query = "SELECT 
    rc.id as reservation_id,
    rc.contract_date,
    rc.event_name,
    rc.company_name,
    rc.address,
    rc.phone_number,
    rc.email,
    rc.location,
    rc.event_start,
    rc.pax,
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
} else {
    die("Error preparing statement: " . $conn->error);
}

// Add PHP helper functions at the top
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
        'confirmed' => 'bg-green-100 text-green-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'pending cancellation' => 'bg-orange-100 text-orange-800'
    ];
    return $statusClasses[strtolower($status)] ?? 'bg-gray-100 text-gray-800';
}
?>

<main class="main-content p-6 bg-gray-100 min-h-screen">
    <!-- History button header -->
    <div class="reservations max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">My Catering Reservations</h2>
            <a href="history.php" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-history text-2xl"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                    <!-- Card Summary -->
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-200 cursor-pointer"
                        onclick="openReservationModal(<?php echo htmlspecialchars($row['reservation_id']); ?>)">
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-lg font-bold text-gray-700">Event: <?php echo htmlspecialchars($row['event_name']); ?></p>
                            <span class="px-2 py-1 text-xs font-semibold rounded <?php echo getStatusClass($row['status']); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </div>
                        <div class="space-y-2 mb-4">
                            <p class="text-sm text-gray-600"><strong>Contract Date:</strong> <?php echo formatDate($row['contract_date']); ?></p>
                            <p class="text-sm text-gray-600"><strong>Name:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                            <p class="mr-9"><strong>Total:</strong> ₱<?php echo number_format(htmlspecialchars($row['pax']), 2, '.', ','); ?></p>

                        </div>
                    </div>

                    <!-- Modal Structure -->
                    <div id="reservationModal-<?php echo $row['reservation_id']; ?>" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2 max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold">Catering Event Details</h2>
                                <button onclick="closeReservationModal(<?php echo $row['reservation_id']; ?>)" class="text-gray-500 hover:text-gray-700">&times;</button>
                            </div>

                            <!-- Modal Content -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Company Details -->
                                <div class="space-y-3">
                                    <h3 class="font-semibold text-lg">Company Information</h3>
                                    <p><span class="font-medium">Company:</span> <?php echo htmlspecialchars($row['company_name']); ?></p>
                                    <p><span class="font-medium">Address:</span> <?php echo htmlspecialchars($row['address']); ?></p>
                                    <p><span class="font-medium">Phone:</span> <?php echo htmlspecialchars($row['phone_number']); ?></p>
                                    <p><span class="font-medium">Email:</span> <?php echo htmlspecialchars($row['email']); ?></p>
                                </div>

                                <!-- Event Details -->
                                <div class="space-y-3">
                                    <h3 class="font-semibold text-lg">Event Information</h3>
                                    <p><span class="font-medium">Location:</span> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p><span class="font-medium">Start:</span> <?php echo formatDateTime($row['event_start']); ?></p>
                                    <p><span class="font-medium">Pax:</span> <?php
                                                                                $total = $row['pax'];
                                                                                if ($total == 210000) {
                                                                                    echo "200";
                                                                                } elseif ($total == 170000) {
                                                                                    echo "150";
                                                                                } elseif ($total == 140000) {
                                                                                    echo "100";
                                                                                }
                                                                                ?> Pax</p>
                                    <p class="mr-9"><strong>Total:</strong> ₱<?php echo number_format(htmlspecialchars($row['pax']), 2, '.', ','); ?></p>

                                </div>

                                <!-- Menu Items -->
                                <div class="col-span-2">
                                    <h3 class="font-semibold text-lg mb-3">Menu Items</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <?php
                                        $menu_items = explode(', ', $row['menu_items']);
                                        $quantities = explode(', ', $row['menu_quantities']);
                                        foreach ($menu_items as $index => $item) {
                                            echo "<div class='bg-gray-50 p-2 rounded-lg text-sm'>
                        • " . htmlspecialchars($item) . " (x" . htmlspecialchars($quantities[$index]) . ")
                    </div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Cancel Button -->
                            <?php if (strtolower($row['status']) === 'pending'): ?>
                                <div class="mt-6 flex gap-2">
                                    <button onclick="openCancelModal(<?php echo $row['reservation_id']; ?>)"
                                        class="w-full bg-red-50 text-red-600 hover:bg-red-100 py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                        <i class="fas fa-times-circle"></i> Cancel Reservation
                                    </button>
                                    <button onclick="openRescheduleModal(<?php echo $row['reservation_id']; ?>)"
                                        class="w-full bg-yellow-50 text-yellow-600 hover:bg-yellow-100 py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                        <i class="fas fa-calendar-alt"></i> Reschedule
                                    </button>
                                </div>
                            <?php elseif (strtolower($row['status']) === 'pending cancellation'): ?>
                                <div class="mt-6">
                                    <a href="cater_cancelcancel.php?reservation_id=<?php echo $row['reservation_id']; ?>"
                                        class="w-full bg-blue-50 text-blue-600 hover:bg-blue-100 py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 no-underline">
                                        <i class="fas fa-undo"></i> Cancel Cancellation Request
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div class="col-span-full text-center py-8 text-gray-500">No catering reservations found.</div>';
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</main><!-- Reschedule Modal -->
<div id="rescheduleModal" class="fixed inset-0 bg-gray-800 mt-5 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Reschedule Reservation</h2>
            <button onclick="closeRescheduleModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form id="rescheduleForm" method="POST" action="resched_cater.php" onsubmit="return validateEventTimes()">
            <input type="hidden" name="reservation_id" id="rescheduleReservationId">
            <div class="mt-2">
                <label for="event_start" class="block text-sm font-medium text-gray-700">Event Start Time</label>
                <input type="datetime-local"
                    id="event_start"
                    name="event_start"
                    required
                    class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500"
                    onchange="validateEventTimes()">
                <p class="text-sm text-gray-500 mt-1">Note: Catering services must end within a maximum duration of 24 hours from the event start time.</p>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Reschedule</button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Cancel Reservation</h2>
            <button onclick="closeCancelModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form id="cancelForm" method="POST" action="cancel_cater.php">
            <input type="hidden" name="reservation_id" id="cancelReservationId">
            <div class="mt-2">
                <label for="cancel_reason" class="block text-sm font-medium text-gray-700">Reason for Cancellation</label>
                <textarea id="cancel_reason" name="cancel_reason" required class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">Cancel Reservation</button>
            </div>
        </form>
    </div>
</div>

<script>
    function validateEventTimes() {
        const startDateInput = document.getElementById('event_start');
        const startDate = new Date(startDateInput.value);
        const now = new Date();

        // Calculate one week from now
        const oneWeekFromNow = new Date();
        oneWeekFromNow.setDate(oneWeekFromNow.getDate() + 7);

        // Clear previous validations
        startDateInput.setCustomValidity('');

        // Check if start date is at least one week in the future
        if (startDate < oneWeekFromNow) {
            startDateInput.setCustomValidity('Event must be scheduled at least 1 week in advance');
            alert('Event must be scheduled at least 1 week in advance');
            return false;
        }

        return true;
    }

    function openReservationModal(id) {
        document.getElementById(`reservationModal-${id}`).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeReservationModal(id) {
        document.getElementById(`reservationModal-${id}`).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openRescheduleModal(reservationId) {
        closeReservationModal(reservationId);
        document.getElementById('rescheduleReservationId').value = reservationId;
        document.getElementById('rescheduleModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openCancelModal(reservationId) {
        closeReservationModal(reservationId);
        document.getElementById('cancelReservationId').value = reservationId;
        document.getElementById('cancelModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>