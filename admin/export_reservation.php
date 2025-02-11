<?php
include 'conn.php'; // Assuming you have a connection to the database

if (isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);

    $query = "
        SELECT 
            r.reservation_id, r.user_id, r.reservation_date, r.reservation_time, r.dine_in_or_takeout, r.takeout_type, r.delivery_location, r.guests, r.payment_method, r.special_requests, r.status, r.created_at,
            u.name AS user_name, u.email AS user_email, u.phone,  
            GROUP_CONCAT(m.name SEPARATOR ', ') AS menu_items,
            GROUP_CONCAT(m.image_url SEPARATOR ', ') AS menu_images,
            GROUP_CONCAT(m.price SEPARATOR ', ') AS menu_prices,
            GROUP_CONCAT(p.name SEPARATOR ', ') AS promo_items,
            GROUP_CONCAT(p.image_url SEPARATOR ', ') AS promo_images,
            GROUP_CONCAT(p.price SEPARATOR ', ') AS promo_prices
        FROM 
            reservation r
        JOIN 
            users u ON r.user_id = u.user_id
        LEFT JOIN 
            reservation_menu rm ON r.reservation_id = rm.reservation_id
        LEFT JOIN 
            menu m ON rm.menu_item_id = m.id
        LEFT JOIN 
            promotions p ON rm.promo_item_id = p.id
        WHERE 
            r.reservation_id = ?
        GROUP BY 
            r.reservation_id
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();

    if ($reservation) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=reservation_' . $reservation_id . '.csv');

        $output = fopen('php://output', 'w');

        // Write the header
        fputcsv($output, array('Reservation ID', 'User ID', 'Reservation Date', 'Reservation Time', 'Dine In or Takeout', 'Takeout Type', 'Delivery Location', 'Guests', 'Payment Method', 'Special Requests', 'Status', 'Created At', 'User Name', 'User Email', 'Phone', 'Item Type', 'Item Name', 'Item Image', 'Item Price'));

        // Write the reservation details
        $menu_items = explode(', ', $reservation['menu_items']);
        $menu_images = explode(', ', $reservation['menu_images']);
        $menu_prices = explode(', ', $reservation['menu_prices']);
        $promo_items = explode(', ', $reservation['promo_items']);
        $promo_images = explode(', ', $reservation['promo_images']);
        $promo_prices = explode(', ', $reservation['promo_prices']);

        // Write menu items
        foreach ($menu_items as $index => $menu_item) {
            fputcsv($output, array(
                $reservation['reservation_id'],
                $reservation['user_id'],
                $reservation['reservation_date'],
                $reservation['reservation_time'],
                $reservation['dine_in_or_takeout'],
                $reservation['takeout_type'],
                $reservation['delivery_location'],
                $reservation['guests'],
                $reservation['payment_method'],
                $reservation['special_requests'],
                $reservation['status'],
                $reservation['created_at'],
                $reservation['user_name'],
                $reservation['user_email'],
                $reservation['phone'],
                'Menu Item',
                $menu_item,
                $menu_images[$index],
                $menu_prices[$index]
            ));
        }

        // Write promo items
        foreach ($promo_items as $index => $promo_item) {
            fputcsv($output, array(
                $reservation['reservation_id'],
                $reservation['user_id'],
                $reservation['reservation_date'],
                $reservation['reservation_time'],
                $reservation['dine_in_or_takeout'],
                $reservation['takeout_type'],
                $reservation['delivery_location'],
                $reservation['guests'],
                $reservation['payment_method'],
                $reservation['special_requests'],
                $reservation['status'],
                $reservation['created_at'],
                $reservation['user_name'],
                $reservation['user_email'],
                $reservation['phone'],
                'Promo Item',
                $promo_item,
                $promo_images[$index],
                $promo_prices[$index]
            ));
        }

        fclose($output);
    } else {
        echo "No reservation found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>