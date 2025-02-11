<?php
session_start();
include 'conn.php';
$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        rc.*,
        u.name AS user_name, 
        u.email AS user_email, 
        u.phone,
        GROUP_CONCAT(m.name SEPARATOR ', ') AS menu_items,
        GROUP_CONCAT(m.image_url SEPARATOR ', ') AS menu_images,
        GROUP_CONCAT(m.price SEPARATOR ', ') AS menu_prices,
        GROUP_CONCAT(rcm.quantity SEPARATOR ', ') AS menu_quantities
    FROM reservation_catering rc
    JOIN users u ON rc.user_id = u.user_id
    LEFT JOIN reservation_menu rcm ON rc.id = rc.id
    LEFT JOIN menu m ON rc.id = m.id
    WHERE rc.user_id = ? AND rc.status = 'Pending'
    GROUP BY rc.id
    ORDER BY rc.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$caterings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate total amount for each catering
foreach ($caterings as &$catering) {
    $menu_items = explode(', ', $catering['menu_items']);
    $menu_prices = explode(', ', $catering['menu_prices']);
    $menu_quantities = explode(', ', $catering['menu_quantities']);

    $total = 0;
    foreach ($menu_items as $index => $item) {
        if (isset($menu_prices[$index]) && isset($menu_quantities[$index])) {
            $total += floatval($menu_prices[$index]) * intval($menu_quantities[$index]);
        }
    }
    $catering['total'] = $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Catering Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-red-800 text-white">
    <main class="main-content p-6 min-h-screen">
        <div class="reservations max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <a href="userdashboard.php" class="text-white hover:text-gray-300">
                    <i class="fas fa-arrow-left text-2xl"></i>
                </a>
            </div>
            <h1 class="text-4xl text-center mb-4 font-bold">Catering Details</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($caterings)): ?>
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center">
                        <p class="text-xl font-semibold">No Catering Reservations</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($caterings as $catering): ?>
                        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 relative border border-gray-200 cursor-pointer text-gray-900"
                            onclick="openCateringModal(<?php echo htmlspecialchars($catering['id']); ?>)">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-lg font-bold">Catering ID: <?php echo htmlspecialchars($catering['id']); ?></p>
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Active Catering</span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <p class="text-sm"><strong>Contract Date:</strong> <?php echo htmlspecialchars($catering['contract_date']); ?></p>
                                <p class="text-sm"><strong>Event Date:</strong> <?php echo htmlspecialchars($catering['event_start']); ?></p>
                                <p class="text-sm"><strong>Event Type:</strong> <?php echo htmlspecialchars($catering['event_name']); ?></p>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <p class="text-lg font-bold">Total Amount: ₱<?php echo number_format($catering['cost'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Structure -->
    <div id="uniqueCateringModal" class="fixed inset-0 bg-gray-800 mt-5 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2 text-gray-900">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Catering Details</h2>
                <button onclick="closeCateringModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        function openCateringModal(cateringId) {
            const catering = <?php echo json_encode($caterings); ?>.find(c => c.id == cateringId);
            const modalContent = `
                <div class="mt-2 scrollable-content">
                    <h3 class="text-lg font-bold mb-4">Catering #${catering.id}</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="space-y-2">
                            <p class="text-sm"><span class="font-medium text-gray-500">Event Date:</span> ${catering.event_date}</p>
                            <p class="text-sm"><span class="font-medium text-gray-500">Event Time:</span> ${catering.event_time}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm"><span class="font-medium text-gray-500">Event Type:</span> ${catering.event_type}</p>
                            <p class="text-sm"><span class="font-medium text-gray-500">Guests:</span> ${catering.number_of_guests}</p>
                            <p class="text-sm"><span class="font-medium text-gray-500">Venue:</span> ${catering.venue}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500 mb-2">Menu Items:</p>
                        <div class="grid grid-cols-3 gap-2">
                            ${catering.menu_items.split(', ').map((item, index) => `
                                <div class="relative group">
                                    <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                        <img src="uploads/${catering.menu_images.split(', ')[index]}" alt="${item}" class="w-full h-20 object-cover">
                                    </div>
                                    <div class="mt-1 text-xs text-gray-600">
                                        <p class="font-medium truncate">${item}</p>
                                        <p>₱${parseFloat(catering.menu_prices.split(', ')[index]).toFixed(2)} x ${catering.menu_quantities.split(', ')[index]}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Total Amount:</span>
                            <span class="text-lg font-bold text-gray-900">₱${parseFloat(catering.total).toFixed(2)}</span>
                        </div>
                    </div>
                    ${catering.special_requests ? `
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Special Requests:</p>
                            <p class="text-sm text-gray-900 mt-1">${catering.special_requests}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('uniqueCateringModal').classList.remove('hidden');
        }

        function closeCateringModal() {
            document.getElementById('uniqueCateringModal').classList.add('hidden');
        }
    </script>
</body>
</html>