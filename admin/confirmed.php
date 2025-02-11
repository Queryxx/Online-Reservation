<div id="confirmed" class="tab-content pt-4 hidden">
    <div class="card-container">
        <?php foreach ($reservations as $reservation): ?>
            <?php if (strtolower($reservation['status']) === 'confirmed'): ?>
                <div class="card" data-bs-toggle="modal"
                    data-bs-target="#confirmedReservationModal<?php echo $reservation['reservation_id']; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Reservation ID: <?php echo htmlspecialchars($reservation['reservation_id']); ?></h5>
                        <span class="badge <?php echo strtolower($reservation['status']); ?>">
                            <?php echo htmlspecialchars($reservation['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($reservation['user_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($reservation['user_email']); ?></p>
                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($reservation['phone']); ?></p>
                        <button type="button" class="btn btn-danger mt-2 p-1" data-bs-toggle="modal"
                            data-bs-target="#paymentconfirmModal<?php echo $reservation['reservation_id']; ?>">
                            <i class="fas fa-receipt"></i> Payment Screenshot
                        </button>

                    </div>
                </div>

                <!-- Payment Modal -->
                <div class="modal fade" id="paymentconfirmModal<?php echo $reservation['reservation_id']; ?>" tabindex="-1"
                    aria-labelledby="paymentModalLabel<?php echo $reservation['reservation_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel<?php echo $reservation['reservation_id']; ?>">
                                    Payment Screenshot</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <?php
                                $payment_screenshot_path = '../' . htmlspecialchars($reservation['payment_screenshot']);
                                ?>
                                <img src="<?php echo $payment_screenshot_path; ?>" alt="Payment Screenshot" class="img-fluid"
                                    style="max-width: 100%; height: auto;">
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .modal-body img {
                        max-width: 100%;
                        height: auto;
                    }
                </style>

                <!-- Modal -->
                <div class="modal fade" id="confirmedReservationModal<?php echo $reservation['reservation_id']; ?>"
                    tabindex="-1" aria-labelledby="confirmedReservationModalLabel<?php echo $reservation['reservation_id']; ?>"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"
                                    id="confirmedReservationModalLabel<?php echo $reservation['reservation_id']; ?>">Reservation
                                    Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex flex-column flex-md-row">
                                    <div class="flex-1 p-2">
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($reservation['user_name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($reservation['user_email']); ?>
                                        </p>
                                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($reservation['phone']); ?>
                                        </p>
                                        <p><strong>Date:</strong>
                                            <?php echo htmlspecialchars($reservation['reservation_date']); ?></p>
                                        <p><strong>Time:</strong>
                                            <?php echo htmlspecialchars($reservation['reservation_time']); ?></p>
                                        <p><strong>Guests:</strong> <?php echo htmlspecialchars($reservation['guests']); ?></p>
                                        <p><strong>Type:</strong>
                                            <?php echo htmlspecialchars($reservation['dine_in_or_takeout']); ?></p>
                                        <p><strong>Take Out Type:</strong>
                                            <?php echo htmlspecialchars($reservation['takeout_type']) ?: ''; ?></p>
                                        <p><strong>Delivery Location:</strong>
                                            <?php echo htmlspecialchars($reservation['delivery_location'] ?: ''); ?></p>
                                        <p><strong>Special Request:</strong>
                                            <?php echo htmlspecialchars($reservation['special_requests'] ?: ''); ?></p>
                                        <?php if (!empty($reservation['cancel_reason'])): ?>
                                            <p><strong>Reason for Cancellation:</strong>
                                                <?php echo htmlspecialchars($reservation['cancel_reason']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 p-2">
                                        <p><strong>Menu Items:</strong></p>
                                        <div class="categories-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
                                            <?php
                                            $menu_items = array_filter(explode(', ', $reservation['menu_items']));
                                            $menu_images = array_filter(explode(', ', $reservation['menu_images']));
                                            $menu_prices = array_filter(explode(', ', $reservation['menu_prices']));
                                            $menu_quantities = array_filter(explode(', ', $reservation['menu_quantities']));
                                            $menu_categories = array_filter(explode(', ', $reservation['menu_categories']));
                                            $promo_images = array_filter(explode(', ', $reservation['promo_images']));
                                            $promo_prices = array_filter(explode(', ', $reservation['promo_prices']));

                                            $items = [];
                                            if (!empty($menu_items)) {
                                                $items = array_map(function ($item, $image, $price, $quantity, $category) {
                                                    return [
                                                        'type' => 'menu',
                                                        'item' => $item,
                                                        'image' => $image,
                                                        'price' => $price,
                                                        'quantity' => $quantity,
                                                        'category' => $category
                                                    ];
                                                }, $menu_items, $menu_images, $menu_prices, $menu_quantities, $menu_categories);
                                            }

                                            // Only add promo items if there are actual promo items (not empty strings)
                                            if (!empty($promo_images) && !empty($promo_prices)) {
                                                $promo_items = array_map(function ($image, $price) {
                                                    return [
                                                        'type' => 'promo',
                                                        'item' => 'Promo Item',
                                                        'image' => $image,
                                                        'price' => $price,
                                                        'quantity' => 1,
                                                        'category' => 'Promo'
                                                    ];
                                                }, $promo_images, $promo_prices);

                                                if (!empty($promo_items)) {
                                                    $items = array_merge($items, $promo_items);
                                                }
                                            }

                                            $total_price = 0;
                                            foreach ($items as $item) {
                                                if (!empty($item['item']) && !empty($item['price']) && !empty($item['quantity'])) {
                                                    $total_price += floatval($item['price']) * intval($item['quantity']);
                                                }
                                            }

                                            // Group items by category
                                            $grouped_items = [];
                                            foreach ($items as $item) {
                                                if (!empty($item['item']) && !empty($item['price']) && !empty($item['quantity'])) {
                                                    $grouped_items[$item['category']][] = $item;
                                                }
                                            }

                                            // Display items grouped by category
                                            foreach ($grouped_items as $category => $items_in_category): ?>
                                                <div class="category-column" style="flex: 1; min-width: 200px; margin-right: 20px;">
                                                    <p class="category-label" style="color: #007bff; text-transform: uppercase;"><strong><?php echo htmlspecialchars($category); ?></strong></p>
                                                    <ul>
                                                        <?php foreach ($items_in_category as $item): ?>
                                                            <li class="menu-item">
                                                                <?php if (!empty($item['image'])): ?>
                                                                    <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                                                        alt="<?php echo htmlspecialchars($item['item']); ?>" class="w-12 h-auto">
                                                                <?php endif; ?>
                                                                <span>
                                                                    <?php echo htmlspecialchars($item['item']); ?> -
                                                                    ₱<?php echo number_format(floatval($item['price']), 2); ?> x
                                                                    <?php echo intval($item['quantity']); ?>
                                                                </span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                            <p class="mr-9"><strong>Total:</strong> ₱<?php echo number_format($total_price, 2); ?></p>
                                <div class="reservation-actions">
                                    <a href="#" onclick="return confirmDeletion(<?php echo $reservation['reservation_id']; ?>)"
                                        class="btn btn-secondary btn-sm reservation-btn">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                    <a href="export_reservation.php?id=<?php echo $reservation['reservation_id']; ?>"
                                        class="btn btn-secondary btn-sm reservation-btn">
                                        <i class="fas fa-download"></i> Export CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>