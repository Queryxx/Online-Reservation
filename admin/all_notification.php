<?php
session_start();

// Authentication check
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include 'conn.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;
$query = "
    SELECT 
        'reservation' as type,
        r.reservation_id as id,
        r.status,
        r.created_at,
        u.name AS user_name,
        r.reservation_date,
        r.reservation_time,
        r.dine_in_or_takeout
    FROM reservation r
    JOIN users u ON r.user_id = u.user_id
    
    UNION
    SELECT 
    'catering' as type,
    rc.id as id,
    rc.status,
    rc.created_at,
    u.name as user_name,
    rc.event_start as reservation_date,
    rc.event_start as reservation_time,
    CONCAT(rc.event_name, ' - ', rc.company_name) as dine_in_or_takeout
FROM reservation_catering rc
JOIN users u ON rc.user_id = u.user_id
WHERE rc.status = 'Pending'
    UNION
    
    SELECT 
        'message' as type,
        m.message_id as id,
        CASE WHEN m.is_read = 0 THEN 'Unread' ELSE 'Read' END as status,
        m.created_at,
        u.name as user_name,
        NULL as reservation_date,
        NULL as reservation_time,
        m.message as dine_in_or_takeout
    FROM contact_messages m
    JOIN users u ON m.user_id = u.user_id
    ORDER BY created_at DESC
    LIMIT $start, $limit
";


// Update the total records query
$total_records_query = "
    SELECT COUNT(*) as count FROM (
        SELECT reservation_id FROM reservation
        UNION
        SELECT id FROM reservation_catering
        UNION
        SELECT message_id FROM contact_messages
    ) as combined
";
$result = $conn->query($query);
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// Get total records for pagination
$total_records_query = "SELECT COUNT(*) as count FROM reservation";
$total_records = $conn->query($total_records_query)->fetch_assoc()['count'];
$total_pages = ceil($total_records / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .notification-card {
            transition: transform 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .status-pending {
            color: #ffc107;
        }

        .status-approved {
            color: #28a745;
        }

        .status-rejected {
            color: #dc3545;
        }

        a:link {
            color: maroon;
        }

        /* Message status colors */
        .status-unread {
            color: #0d6efd;
        }

        .status-read {
            color: #6c757d;
        }
    </style>
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>All Notifications</h2>
            <a href="dashboard.php" class="btn btn-danger"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>


        <div class="row">
            <?php foreach ($notifications as $notification): ?>
                <div class="col-md-6 mb-3">
                    <?php
                    $link_url = match ($notification['type']) {
                        'reservation' => "view_notif.php?id=" . $notification['id'],
                        'catering' => "view_catering.php?id=" . $notification['id'],
                        'message' => "view_message.php?id=" . $notification['id'],
                    };

                    ?>
                    <a href="<?php echo $link_url; ?>" class="text-decoration-none">
                        <div class="card notification-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title"><?php echo htmlspecialchars($notification['user_name']); ?></h5>
                                    <span class="status-<?php echo strtolower($notification['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($notification['status'])); ?>
                                    </span>
                                </div>
                                <?php if ($notification['type'] === 'reservation'): ?>
                                    <p class="card-text">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('F d, Y', strtotime($notification['reservation_date'])); ?> at
                                        <?php echo date('g:i A', strtotime($notification['reservation_time'])); ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-utensils"></i>
                                        <?php echo ucfirst(htmlspecialchars($notification['dine_in_or_takeout'])); ?>
                                    </p>
                                <?php elseif ($notification['type'] === 'catering'): ?>
                                    <p class="card-text">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('F d, Y', strtotime($notification['reservation_date'])); ?> at
                                        <?php echo date('g:i A', strtotime($notification['reservation_time'])); ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-utensils"></i>
                                        Event Type: <?php echo ucfirst(htmlspecialchars($notification['dine_in_or_takeout'])); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="card-text">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($notification['dine_in_or_takeout']); ?>
                                    </p>
                                <?php endif; ?>
                                <small class="text-muted">
                                    Created: <?php echo date('M d, Y g:i A', strtotime($notification['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>