<div id="confirmed" class="tab-content pt-4 hidden">
    <div class="card-container">
        <?php foreach ($reservations as $reservation): ?>
            <?php if ($reservation['status'] === 'Confirmed'): ?>
                <div class="card" data-bs-toggle="modal"
                    data-bs-target="#confirmReservationModal<?php echo $reservation['id']; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Catering ID: <?php echo htmlspecialchars($reservation['id']); ?></h5>
                        <span class="badge <?php echo strtolower($reservation['status']); ?>">
                            <?php echo htmlspecialchars($reservation['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p><strong>Event:</strong> <?php echo htmlspecialchars($reservation['event_name']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($reservation['company_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($reservation['event_start']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($reservation['location']); ?></p>
                        <button type="button" class="btn btn-danger mt-2 p-1" data-bs-toggle="modal"
                            data-bs-target="#paymentModal<?php echo $reservation['id']; ?>">
                            <i class="fas fa-receipt"></i> Payment Screenshot
                        </button>
                    </div>
                </div>

                <!-- Payment Modal -->
                <div class="modal fade" id="paymentModal<?php echo $reservation['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Payment Screenshot</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="../<?php echo htmlspecialchars($reservation['payment_screenshot']); ?>"
                                    alt="Payment Screenshot" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Full Details Modal -->
                <div class="modal fade" id="confirmReservationModal<?php echo $reservation['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Catering Reservation Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex flex-column flex-md-row">
                                    <!-- Event Details -->
                                    <div class="flex-1 p-2">
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($reservation['user_name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($reservation['user_email']); ?>
                                        </p><br>
                                        <h5 class="text-red-800"><strong>EVENT INFORMATION</strong></h5>
                                        <p><strong>Event Name:</strong> <?php echo htmlspecialchars($reservation['event_name']); ?></p>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($reservation['company_name']); ?></p>
                                        <p><strong>Address:</strong> <?php echo htmlspecialchars($reservation['address']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($reservation['phone_number']); ?></p>
                                        <p><strong>Location:</strong> <?php echo htmlspecialchars($reservation['location']); ?></p>
                                        <p><strong>Start:</strong> <?php echo htmlspecialchars($reservation['event_start']); ?></p>
                                        <p><strong>Pax:</strong>
                                            <?php
                                            $total = $reservation['pax'];
                                            if ($total == 210000) {
                                                echo "200";
                                            } elseif ($total == 170000) {
                                                echo "150";
                                            } elseif ($total == 140000) {
                                                echo "100";
                                            }
                                            ?> Pax
                                        </p>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="flex-1 p-2">
                                        <h6>Selected Menu Items</h6>
                                        <?php
                                        $menu_items = explode(', ', $reservation['menu_items']);
                                        $menu_images = explode(', ', $reservation['menu_images']);
                                        $menu_quantities = explode(', ', $reservation['menu_quantities']);
                                        $menu_categories = explode(', ', $reservation['menu_categories']);

                                        $grouped_items = [];
                                        for ($i = 0; $i < count($menu_items); $i++) {
                                            $category = $menu_categories[$i];
                                            $grouped_items[$category][] = [
                                                'name' => $menu_items[$i],
                                                'image' => $menu_images[$i],
                                                'quantity' => $menu_quantities[$i]
                                            ];
                                        }

                                        foreach ($grouped_items as $category => $items): ?>
                                            <div class="category-section mb-3">
                                                <h6 class="text-primary mt-2" style="color: #007bff; text-transform: uppercase;"><?php echo htmlspecialchars($category); ?></h6>
                                                <div class="menu-items">
                                                    <?php foreach ($items as $item): ?>
                                                        <div class="menu-item d-flex align-items-center mb-2">
                                                            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                                                class="menu-thumb mr-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                            <p class="mr-9"><strong>Total:</strong> ₱<?php echo number_format(htmlspecialchars($reservation['pax']), 2, '.', ','); ?></p>
                             
                                <div class="reservation-actions">
                                    <a href="#" onclick="return confirmDeletion(<?php echo $reservation['id']; ?>)"
                                        class="btn btn-secondary btn-sm reservation-btn">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                    <a href="export_cateringres.php?id=<?php echo $reservation['id']; ?>"
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