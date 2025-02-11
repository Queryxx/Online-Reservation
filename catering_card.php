<?php
session_start();
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
} else {
    die("Error preparing statement: " . $conn->error);
}

// Add PHP helper functions at the top
function formatDate($dateString) {
    if (!$dateString) return '';
    return date('F j, Y', strtotime($dateString));
}

function formatDateTime($dateTimeString) {
    if (!$dateTimeString) return '';
    return date('F j, Y g:i A', strtotime($dateTimeString));
}

function formatNumber($number) {
    return number_format($number, 2);
}

function getStatusClass($status) {
    $statusClasses = [
        'confirmed' => 'bg-green-100 text-green-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'cancelled' => 'bg-red-100 text-red-800'
    ];
    return $statusClasses[strtolower($status)] ?? 'bg-gray-100 text-gray-800';
}
?>

<main class="main-content p-6 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Catering Reservations</h1>
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
                            <p class="text-sm text-gray-600"><strong>Date:</strong> <?php echo formatDate($row['contract_date']); ?></p>
                            <p class="text-sm text-gray-600"><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                        </div>
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-lg font-bold text-gray-900">Total: ₱<?php echo formatNumber($row['cost']); ?></p>
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
                                    <p><span class="font-medium">Owner:</span> <?php echo htmlspecialchars($row['owner']); ?></p>
                                    <p><span class="font-medium">Phone:</span> <?php echo htmlspecialchars($row['phone_number']); ?></p>
                                    <p><span class="font-medium">Email:</span> <?php echo htmlspecialchars($row['email']); ?></p>
                                    <p><span class="font-medium">BIR:</span> <?php echo htmlspecialchars($row['bir_permit']); ?></p>
                                </div>

                                <!-- Event Details -->
                                <div class="space-y-3">
                                    <h3 class="font-semibold text-lg">Event Information</h3>
                                    <p><span class="font-medium">Location:</span> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p><span class="font-medium">Start:</span> <?php echo formatDateTime($row['event_start']); ?></p>
                                    <p><span class="font-medium">End:</span> <?php echo formatDateTime($row['event_end']); ?></p>
                                    <p><span class="font-medium">Guests:</span> <?php echo htmlspecialchars($row['guests']); ?></p>
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

                                <!-- Services -->
                                <div class="col-span-2">
                                    <h3 class="font-semibold text-lg mb-3">Services Included</h3>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <?php echo nl2br(htmlspecialchars($row['services'])); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancel Button -->
                            <?php if ($row['status'] === 'pending'): ?>
                            <div class="mt-6 flex justify-end">
                                <button onclick="openCancelModal(<?php echo $row['reservation_id']; ?>)" 
                                        class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition-colors">
                                    Cancel Reservation
                                </button>
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
</main>

<script>
function openReservationModal(id) {
    document.getElementById(`reservationModal-${id}`).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReservationModal(id) {
    document.getElementById(`reservationModal-${id}`).classList.add('hidden');
    document.body.style.overflow = 'auto';
}
</script>