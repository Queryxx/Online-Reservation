<main class="main-content p-6">
    <!-- Overview Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <a href="orders.php" class="bg-white rounded-lg shadow p-6 block">
            <div class="flex items-center">
                <i class="fas fa-shopping-bag text-3xl text-red-600"></i>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm">Total Orders</h3>
                    <p class="text-2xl font-semibold"><?php echo $total_orders; ?></p>
                </div>
            </div>
        </a>

        <a href="active_reserve.php" class="bg-white rounded-lg shadow p-6 block">
            <div class="flex items-center">
                <i class="fas fa-calendar-check text-3xl text-green-600"></i>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm">Pending Reservation Meal</h3>
                    <p class="text-2xl font-semibold"><?php echo $pending_reservations; ?></p>
                </div>
            </div>
        </a>
        <a href="active_cater.php" class="bg-white rounded-lg shadow p-6 block">
            <div class="flex items-center">
                <i class="fas fa-calendar-check text-3xl text-green-600"></i>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm">Pending Reservation Catering</h3>
                    <p class="text-2xl font-semibold"><?php echo $pending_catering; ?></p>
                </div>
            </div>
        </a>
    </div>
    <!-- Recent Orders Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Recent Orders</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3">Order ID</th>
                            <th class="text-left py-3">Date</th>
                            <th class="text-left py-3">Status</th>
                            <th class="text-left py-3">Total</th>
                            <th class="text-left py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order):
                            $statusColor = match ($order['status']) {
                                'Pending' => 'yellow',
                                'Confirmed' => 'green',
                                'Cancelled' => 'red',
                                'Completed' => 'blue',
                                default => 'gray'
                            };
                        ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3"><?php echo $order['reservation_id']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <span
                                        class="px-2 py-1 bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800 rounded-full text-sm">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <a href="orders.php?id=<?php echo $order['reservation_id']; ?>"
                                        class="text-red-600 hover:text-red-800">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($upcoming_reservations as $reservation):
            $statusColor = match ($reservation['status']) {
                'Pending' => 'yellow',
                'Confirmed' => 'green',
                default => 'gray'
            };
        ?>
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-semibold">Reservation #<?php echo $reservation['reservation_id']; ?></h3>
                        <p class="text-sm text-gray-500">
                            <?php echo date('F j, Y', strtotime($reservation['reservation_date'])); ?> -
                            <?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?>
                        </p>
                    </div>
                    <span
                        class="px-2 py-1 bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800 rounded-full text-sm">
                        <?php echo $reservation['status']; ?>
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-sm"><i class="fas fa-users mr-2"></i><?php echo $reservation['guests']; ?>
                        Persons
                    </p>
                    <p class="text-sm"><i
                            class="fas fa-utensils mr-2"></i><?php echo $reservation['dine_in_or_takeout']; ?></p>
                </div>
                <a href="orders.php?id=<?php echo $reservation['reservation_id']; ?>"
                    class="mt-4 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 inline-block text-center">
                    View Details
                </a>
            </div>
        <?php endforeach; ?>
        <?php foreach ($upcoming_catering as $catering):
            $statusColor = match ($catering['status']) {
                'Pending' => 'yellow',
                'Confirmed' => 'green',
                default => 'gray'
            };
        ?>
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-semibold">Reservation #<?php echo $catering['id']; ?></h3>
                        <p class="text-sm text-gray-500">
                            <?php echo date('F j, Y', strtotime($catering['contract_date'])); ?> -
                            <?php echo date('g:i A', strtotime($catering['event_start'])); ?>
                        </p>
                    </div>
                    <span
                        class="px-2 py-1 bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800 rounded-full text-sm">
                        <?php echo $catering['status']; ?>
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-sm"><i class="fas fa-box mr-2"></i>
                        <?php
                        $total = $catering['pax'];
                        if ($total == 210000) {
                            echo "200";
                        } elseif ($total == 170000) {
                            echo "150";
                        } elseif ($total == 140000) {
                            echo "100";
                        }
                        ?> Pax
                    </p>
                    <p class="text-sm"><i
                            class="fas fa-utensils mr-2"></i><?php echo $catering['location']; ?></p>
                    <p class="mr-9"><strong>Total:</strong> ₱<?php echo number_format(htmlspecialchars($catering['pax']), 2, '.', ','); ?></p>

                </div>
                <a href="orders.php?id=<?php echo $catering['id']; ?>"
                    class="mt-4 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 inline-block text-center">
                    View Details
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>