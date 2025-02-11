<?php
session_start();
include 'conn.php';
require 'vendor/autoload.php';

// 1. Authentication check
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please login first to make a catering reservation.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. Initialize variables 
$user_id = $_SESSION['user_id'];
$user_query = "SELECT email, phone FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$email = $user_data['email'];
$phone_number = $user_data['phone'];
$stmt->close();
// Fetch menu items initially
$query = "SELECT * FROM catering_menu WHERE status = 'available'";
$result = mysqli_query($conn, $query);
$menu_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

// 3. Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $contract_date = $_POST['contract_date'];
    $event_name = $_POST['event_name'];
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $location = $_POST['location'];
    $event_start = $_POST['event_start'];
    $pax = $_POST['guests'];
    $status = 'pending';

    $menu_items_selected = isset($_POST['menu_items']) ? $_POST['menu_items'] : [];
    $menu_quantities = isset($_POST['menu_quantities']) ? $_POST['menu_quantities'] : [];

    $payment_screenshot = $_FILES['payment_screenshot'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($payment_screenshot["name"]);

    if (move_uploaded_file($payment_screenshot["tmp_name"], $target_file)) {
        // 4. Database insertion
        try {
            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO reservation_catering (user_id, contract_date, event_name, company_name, address, phone_number, email, location, event_start, pax, status, payment_screenshot) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "issssssssiss", // 12 parameters: i(1) + s(9) + i(1) + s(1) = 12
                $user_id,       // integer
                $contract_date, // string
                $event_name,    // string
                $company_name,  // string
                $address,      // string
                $phone_number, // string
                $email,        // string
                $location,     // string
                $event_start,  // string
                $pax,       // integer
                $status,       // string
                $target_file   // string
            );

            if ($stmt->execute()) {
                $reservation_id = $stmt->insert_id;


                $stmt_menu = $conn->prepare("INSERT INTO reservation_menu (reservation_id, menu_item_id, quantity) VALUES (?, ?, ?)");
                foreach ($menu_items_selected as $menu_item_id) {
                    $quantity = 1; // Fixed quantity
                    $stmt_menu->bind_param("iii", $reservation_id, $menu_item_id, $quantity);
                    $stmt_menu->execute();
                }
                $stmt_menu->close();

                // 5. Send email confirmation
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'victoriagrillrestaurant@gmail.com';
                    $mail->Password = 'rwno bsje uwrx irqy';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('victoriagrillrestaurant@gmail.com', 'Victoria Grill Restaurant');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Catering Request Submission Confirmation';
                    $mail->Body = "
                    Dear Valued Customer,<br><br>
                    Thank you for your catering request at Victoria Grill Restaurant. Your reservation ID is <strong>$reservation_id</strong>. 
                    Your request has been submitted and is currently awaiting approval by our administration team. 
                    Please note that payments made for reservations are non-refundable.<br><br>
                    We will notify you once it has been confirmed.<br><br>
                    Best regards,<br>
                    Victoria Grill Restaurant";

                    $mail->send();
                    $conn->commit();

                    header("Location: reservation_success.php");
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    throw new Exception("Email sending failed: " . $mail->ErrorInfo);
                }
            } else {
                throw new Exception("Failed to insert reservation");
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error uploading payment screenshot.');</script>";
    }
}

// 6. Fetch menu items for display
$query = "SELECT * FROM catering_menu WHERE status = 'available'";
$result = mysqli_query($conn, $query);
$menu_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!-- Rest of your HTML code remains the same -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Victoria Grill Restaurant - Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .bg-maroon-500 {
            background-color: #800000;
        }

        .hover\:bg-maroon-600:hover {
            background-color: #b22222;
        }
    </style>
</head>

<body class="bg-gray-100">

    <?php include 'reservenav.php' ?>

    <!-- Catering Form Section -->
    <section id="catering" class="py-16 bg-white fade-slide-in">
        <div class="container mx-auto flex flex-col md:flex-row items-start justify-between space-y-6 md:space-y-0 md:space-x-6">
            <form action="catering.php" method="POST" class="w-full md:flex p-5 md:space-x-6" onsubmit="return validateForm()" enctype="multipart/form-data">
                <!-- Menu Selection Section -->
                <div class="w-full md:w-1/2 space-y-6 pr-6 pl-6">
                    <h2 class="text-3xl font-bold text-gray-800 text-center">Select Your Menu</h2>
                    <p class="text-gray-600 mt-4 text-center">Choose the items you'd like to add to your catering order.</p>

                    <!-- Total Price Display -->
                    <div class="bg-gray-200 p-4 rounded-lg shadow-md text-center">
                        <h3 class="text-2xl font-bold text-gray-800">Total: ₱<span id="totalPrice">0.00</span></h3>
                    </div>

                    <!-- Category Tabs -->
                    <div class="tabs mt-4 flex justify-center space-x-2 overflow-x-auto">
                        <?php
                        $categories = array('pork', 'chicken', 'beef', 'pasta', 'vegetables');
                        foreach ($categories as $category) : ?>
                            <button type="button" onclick="filterByCategory('<?php echo $category; ?>')" class="category-tab px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-red-500 hover:text-white transition-colors">
                                <?php echo ucfirst($category); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-8">
                        <?php foreach ($menu_items as $item): ?>
                            <div class="card bg-white rounded-lg shadow-md overflow-hidden" data-category="<?php echo $item['category']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>"
                                    alt="<?php echo htmlspecialchars($item['name']); ?>"
                                    class="w-full h-40 object-cover">
                                <div class="p-4">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            id="menu_<?php echo $item['id']; ?>"
                                            name="menu_items[]"
                                            value="<?php echo $item['id']; ?>"
                                            class="h-5 w-5 text-red-500"
                                            data-category="<?php echo $item['category']; ?>"
                                            onchange="handleMenuSelection(this)">
                                        <label for="menu_<?php echo $item['id']; ?>" class="ml-2 text-gray-700">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <script>
                    function filterByCategory(category) {
                        const cards = document.querySelectorAll('.card');
                        cards.forEach(card => {
                            const menuItem = card.querySelector('[id^="menu_"]');
                            if (menuItem && menuItem.dataset.category === category) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        });

                        // Update active tab styling
                        document.querySelectorAll('.category-tab').forEach(tab => {
                            if (tab.textContent.toLowerCase() === category) {
                                tab.classList.add('bg-red-500', 'text-white');
                            } else {
                                tab.classList.remove('bg-red-500', 'text-white');
                            }
                        });
                    }

                    // Modified DOMContentLoaded event listener
                    document.addEventListener('DOMContentLoaded', () => {
                        // Filter by pork category by default
                        filterByCategory('pork');

                        // Find and activate the pork category tab
                        const porkTab = document.querySelector('.category-tab:first-child');
                        if (porkTab) {
                            porkTab.classList.add('bg-red-500', 'text-white');
                        }
                    });
                </script>

                <!-- Catering Form Section -->
                <div class="w-full md:w-1/2 space-y-6 pl-6">
                    <h2 class="text-3xl font-bold mt-5 text-gray-800 text-center">Catering Request</h2>
                    <p class="text-gray-600 mt-4 text-center">Fill out the form below to request catering services from Victoria Grill Restaurant.</p>
                    <!-- Contract Date -->
                    <div>
                        <label for="contract_date" class="block text-sm font-medium text-gray-700">Contract Date</label>
                        <input type="date"
                            id="contract_date"
                            name="contract_date"
                            value="<?php date_default_timezone_set('Asia/Manila');
                                    echo date('Y-m-d'); ?>"
                            readonly
                            class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500 bg-gray-100">
                    </div>

                    <!-- Event Name -->
                    <div class="mt-4">
                        <label for="event_name" class="block text-sm font-medium text-gray-700">Event Name</label>
                        <input type="text" id="event_name" name="event_name" required class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="mt-2">
                        <label for="guests" class="block text-sm font-medium text-gray-700">Select Number of Pax</label>
                        <select
                            id="guests"
                            name="guests"
                            required
                            class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500">
                            <option value="" disabled selected>Select number of pax</option>
                            <option value="140000">100 pax</option>
                            <option value="170000">150 pax</option>
                            <option value="210000">200 pax</option>
                        </select>
                    </div>
                    <!-- Company Information -->
                    <h3 class="text-lg font-semibold mt-6 text-gray-800">Personal Information</h3>

                    <div class="mt-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="company_name" name="company_name" required class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="mt-2">
                        <label for="owner" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" id="address" name="address" required class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="mt-2">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="number"
                            id="phone_number"
                            name="phone_number"
                            value="<?php echo htmlspecialchars($phone_number); ?>"
                            required
                            class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>


                    <!-- Payment Method -->
                    <div>
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-700">Payment Method</label>

                            <div class="mt-1 p-4 block w-full border rounded-md bg-gray-50">
                                <input type="hidden" name="payment" value="Gcash">
                                <p class="text-gray-700">GCash Only</p>
                            </div>
                        </div>

                        <?php
                        // Fetch the payment details for GCash from the database
                        $query = "SELECT * FROM payment_details WHERE payment_method = 'Gcash'";
                        $result = mysqli_query($conn, $query);

                        $gcashDetails = null;

                        while ($row = mysqli_fetch_assoc($result)) {
                            if ($row['payment_method'] == 'Gcash') {
                                $gcashDetails = $row;
                            }
                        }
                        ?>
                        <!-- GCash Details -->
                        <div class="mt-4">
                            <?php if ($gcashDetails): ?>
                                <div class="flex items-center mt-2">
                                    <img src="uploads/<?php echo $gcashDetails['payment_picture']; ?>" alt="Gcash QR Code" class="w-32 h-32 mr-4">
                                    <p class="text-gray-700">GCash Number:<br><strong><?php echo $gcashDetails['phone_number']; ?></strong></p>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center mt-2">
                                    <p class="text-gray-700">GCash details not available. Please contact administrator.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Payment Screenshot -->
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-2">Note: At least 50% of the payment should be paid before the admin allows approval of the reservation for catering. Please note that this payment is non-refundable.</p>
                            <div class="bg-gray-100 p-3 rounded-lg shadow-md">
                                <h5 class="font-bold text-gray-800">Total: ₱<span id="alltotal">0.00</span></h5>
                                <h5 class="font-bold text-gray-800">50% of Total: ₱<span id="halfTotal">0.00</span></h5>
                            </div>
                            <label for="payment_screenshot" class="block text-sm mt-3 font-medium text-gray-700">Payment Screenshot</label>
                            <input type="file"
                                id="payment_screenshot"
                                name="payment_screenshot"
                                accept="image/*"
                                required
                                class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                    <!-- Event Information -->
                    <h3 class="text-lg font-semibold mt-6 text-gray-800">Event Information</h3>
                    <p class="text-sm text-gray-600 mb-2">Note: Terms and agreements are stipulated willingly and accordingly by both contracting parties. Future amendments will not invalidate this contract.</p>
                    <div class="mt-2">
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" id="location" name="location" required class="mt-1 p-3 block w-full border rounded-md focus:ring-red-500 focus:border-red-500">
                        <p class="text-sm text-gray-600 mt-1 mb-2">Note: Additional transportation fee will be charged for events outside Bangued, Abra. The fee varies depending on the location and distance.</p>

                    </div>

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
                    <div class="mt-3 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Services Included:</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Grand Stage Setup</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Grand Stage Table Setup</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Grand Entrance Table Setup</span>
                                </li>
                            </ul>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Tables & Chairs</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Event Coordinator</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Lights & Sounds System</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Special Requests -->
                    <div>
                        <label for="requests" class="block text-sm font-medium text-gray-700">Special Requests</label>
                        <textarea id="requests" name="requests" rows="4" placeholder="Enter any special requests (optional)"
                            class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>
                    <!-- Submit Button -->
                    <div class="text-center mt-6">
                        <button type="submit" class="bg-red-800 hover:bg-red-600 text-white px-6 py-3 rounded-lg text-lg">Submit Contract</button>
                    </div>
            </form>
        </div>
    </section>
    <script>
        document.getElementById("guests").addEventListener("change", function() {
            const paxPrices = {
                140000: 140000,
                170000: 170000,
                210000: 210000
            };

            const selectedPax = this.value;
            const totalPrice = paxPrices[selectedPax] || 0;
            const halfTotal = totalPrice * 0.5;

            document.getElementById("totalPrice").textContent = totalPrice.toLocaleString();
            document.getElementById("alltotal").textContent = totalPrice.toLocaleString();
            document.getElementById("halfTotal").textContent = halfTotal.toLocaleString();
        });
    </script>

    <script>
        // Function to validate the form
        function validateForm() {
            const payment = document.getElementById('payment').value;
            const paymentScreenshot = document.getElementById('payment_screenshot').value;
            if (payment && !paymentScreenshot) {
                alert('Please upload a payment screenshot.');
                return false;
            }

            return true;
        }
        const requiredCategories = ['pork', 'chicken', 'beef', 'pasta', 'vegetables'];

        function validateMenuSelections() {
            const selectedItems = document.querySelectorAll('input[name="menu_items[]"]:checked');
            const selectedCategories = new Set();

            // Get all selected categories
            selectedItems.forEach(item => {
                selectedCategories.add(item.dataset.category);
            });

            // Check if all required categories are selected
            const missingCategories = requiredCategories.filter(category =>
                !selectedCategories.has(category)
            );

            if (missingCategories.length > 0) {
                alert(`Please select one item from each category. Missing: ${missingCategories.join(', ')}`);
                return false;
            }

            return true;
        }

        function handleMenuSelection(checkbox) {
            const category = checkbox.dataset.category;

            // If this checkbox is being checked
            if (checkbox.checked) {
                // Find all other checked checkboxes in the same category
                const checkedBoxes = document.querySelectorAll(`input[name="menu_items[]"][data-category="${category}"]:checked`);

                // If there's already a checked item in this category (excluding the current one)
                if (checkedBoxes.length > 1) {
                    // Uncheck other items in this category
                    checkedBoxes.forEach(box => {
                        if (box !== checkbox) {
                            box.checked = false;
                        }
                    });
                }
            }
        }

        function validateGuests(input) {
            const errorSpan = document.getElementById('guestsError');
            if (parseInt(input.value) > 500) {
                input.value = 500;
                errorSpan.classList.remove('hidden');
                return false;
            }
            errorSpan.classList.add('hidden');
            return true;
        }

        function validateEventTimes() {
            const startDate = new Date(document.getElementById('event_start').value);
            const now = new Date();

            // Calculate one week from now
            const oneWeekFromNow = new Date();
            oneWeekFromNow.setDate(oneWeekFromNow.getDate() + 7);
            oneWeekFromNow.setHours(0, 0, 0, 0); // Set to start of day

            // Clear previous validations
            document.getElementById('event_start').setCustomValidity('');

            // Check if start date is at least one week in the future
            const startDateTime = new Date(startDate);
            startDateTime.setHours(0, 0, 0, 0);

            if (startDateTime < oneWeekFromNow) {
                document.getElementById('event_start').setCustomValidity('Event must be scheduled at least 1 week in advance');
                alert('Event must be scheduled at least 1 week in advance');
                return false;
            }

            return true;
        }

        // Update the event time input when start date changes
        document.getElementById('event_start').addEventListener('change', function() {
            const startDate = new Date(this.value);

            // Set min time as start time
            const minEndTime = new Date(startDate);

            // Set max time as 24 hours from start time
            const maxEndTime = new Date(startDate);
            maxEndTime.setHours(startDate.getHours() + 24);
        });

        // Set minimum dates on page load
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const oneWeekFromNow = new Date(now);
            oneWeekFromNow.setDate(now.getDate() + 7);

            // Format the datetime for the input
            const year = oneWeekFromNow.getFullYear();
            const month = String(oneWeekFromNow.getMonth() + 1).padStart(2, '0');
            const day = String(oneWeekFromNow.getDate()).padStart(2, '0');
            const hours = String(oneWeekFromNow.getHours()).padStart(2, '0');
            const minutes = String(oneWeekFromNow.getMinutes()).padStart(2, '0');

            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

            // Set minimum date for event start
            document.getElementById('event_start').min = minDateTime;

            // Clear any existing values that might be invalid
            document.getElementById('event_start').value = '';
        });

        // Update the existing validateForm function
        const existingValidateForm = validateForm;

        function validateForm() {
            // First check menu selections
            if (!validateMenuSelections()) {
                return false;
            }
            return existingValidateForm();
        }
        // Set minimum date and time on page load
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

            document.getElementById('event_start').min = minDateTime;
        });

        function setMinDate() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('contract_date').min = today;
        }

        document.addEventListener('DOMContentLoaded', function() {
            setMinDate();
            const fadeSlideInElement = document.querySelector('#catering');
            fadeSlideInElement.classList.add('visible');
        });
    </script>
</body>

</html>