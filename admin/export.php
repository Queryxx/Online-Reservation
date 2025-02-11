<?php
include 'conn.php';

// Get the data
$pendingOrdersStmt = $conn->prepare("SELECT COUNT(*) as count FROM reservation WHERE status = 'pending'");
$pendingOrdersStmt->execute();
$pendingOrdersResult = $pendingOrdersStmt->get_result();
$pendingOrdersCount = $pendingOrdersResult->fetch_assoc()['count'];
$pendingOrdersStmt->close();

$salesByDayStmt = $conn->prepare("SELECT COUNT(*) as count FROM reservation WHERE status = 'confirmed' AND reservation_date = CURDATE()");
$salesByDayStmt->execute();
$salesByDayResult = $salesByDayStmt->get_result();
$salesByDayCount = $salesByDayResult->fetch_assoc()['count'];
$salesByDayStmt->close();

$totalSalesStmt = $conn->prepare("SELECT COUNT(*) as count FROM reservation WHERE status = 'confirmed'");
$totalSalesStmt->execute();
$totalSalesResult = $totalSalesStmt->get_result();
$totalSalesCount = $totalSalesResult->fetch_assoc()['count'];
$totalSalesStmt->close();

$conn->close();

// Get the current date
$currentDate = date('Y-m-d');

// Create the Excel file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="sales_data_' . $currentDate . '.xls"');
header('Cache-Control: max-age=0');

echo "Date\tPending Orders\tSales by Day\tTotal Sales\n";
echo "$currentDate\t$pendingOrdersCount\t$salesByDayCount\t$totalSalesCount\n";
?>