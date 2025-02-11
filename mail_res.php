<?php
session_start();
include 'conn.php'; // Assuming you have a connection to the database

// Fetch menu items from your menu table
$query = "SELECT * FROM menu"; // Adjust table name and fields
$result = $conn->query($query);
$menu_items = $result->fetch_all(MYSQLI_ASSOC);

// Fetch promo items from your promo table
$query_promo = "SELECT * FROM promotions"; // Adjust table name and fields
$result_promo = $conn->query($query_promo);
$promo_items = $result_promo->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $date = $_POST['date'];
  $time = $_POST['time'];
  $guests = $_POST['guests'];
  $type = $_POST['type'];
  $takeoutType = isset($_POST['takeoutType']) ? $_POST['takeoutType'] : null;
  $location = $_POST['location'];
  $payment = $_POST['payment'];
  $requests = $_POST['requests'];
  $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
  $menu_items_selected = isset($_POST['menu_items']) ? $_POST['menu_items'] : [];
  $promo_items_selected = isset($_POST['promoitems']) ? $_POST['promoitems'] : [];
  $menu_quantities = isset($_POST['menu_quantities']) ? $_POST['menu_quantities'] : [];
  $promo_quantities = isset($_POST['promo_quantities']) ? $_POST['promo_quantities'] : [];
  $payment_screenshot = $_FILES['payment_screenshot'];

  // Debugging: Print the values to check if they are correct
  error_log("Type: $type, Takeout Type: $takeoutType, Location: $location");

  // Handle file upload
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($payment_screenshot["name"]);
  move_uploaded_file($payment_screenshot["tmp_name"], $target_file);

  // Insert reservation into the database
  $stmt = $conn->prepare("INSERT INTO reservation (user_id, reservation_date, reservation_time, dine_in_or_takeout, takeout_type, delivery_location, guests, payment_method, special_requests, payment_screenshot, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
  $stmt->bind_param("isssssssss", $user_id, $date, $time, $type, $takeoutType, $location, $guests, $payment, $requests, $target_file);

  if ($stmt->execute()) {
    $reservation_id = $stmt->insert_id;

    // Insert selected menu items into the reservation_menu table
    $stmt_menu = $conn->prepare("INSERT INTO reservation_menu (reservation_id, menu_item_id, promo_item_id, quantity) VALUES (?, ?, ?, ?)");
    // Loop for inserting menu items with promo_item_id as null
    foreach ($menu_items_selected as $index => $menu_item_id) {
      $promo_item_id = null;
      $quantity = $menu_quantities[$index];
      $stmt_menu->bind_param("iiii", $reservation_id, $menu_item_id, $promo_item_id, $quantity);
      $stmt_menu->execute();
    }

    // Loop for inserting promo items with menu_item_id as null
    foreach ($promo_items_selected as $index => $promo_item_id) {
      $menu_item_id = null;
      $quantity = $promo_quantities[$index];
      $stmt_menu->bind_param("iiii", $reservation_id, $menu_item_id, $promo_item_id, $quantity);
      $stmt_menu->execute();
    }

    $stmt_menu->close();

    // Redirect to a confirmation page or display a success message
    header("Location: reservation_success.php");
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserve a Table - Victoria Grill Restaurant</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="style/menu.css">
</head>

<body class="bg-gray-100">
  <?php include 'reservenav.php'; ?>

  <!-- Reservation Form Section -->
  <section id="reservation" class="py-16 bg-white">
    <div
      class="container mx-auto flex flex-col md:flex-row items-start justify-between space-y-6 md:space-y-0 md:space-x-6">
      <form action="reserveform.php" method="POST" class="w-full md:flex p-5 md:space-x-6"
        onsubmit="return validateForm()" enctype="multipart/form-data">
        <!-- Menu Selection Section -->
        <div class="w-full md:w-1/2 space-y-6 pr-6 pl-6">
          <h2 class="text-3xl font-bold text-gray-800 text-center">Select Your Menu</h2>
          <p class="text-gray-600 mt-4 text-center">Choose the items you'd like to add to your reservation.</p>

          <!-- Total Price Display -->
          <div class="bg-gray-200 p-4 rounded-lg shadow-md text-center">
            <h3 class="text-2xl font-bold text-gray-800">Total: ₱<span id="totalPrice">0.00</span></h3>
          </div>

          <!-- Menu and Promo Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-8">
            <?php
            foreach ($promo_items as $item):
              ?>
              <div class="card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>"
                  alt="<?php echo htmlspecialchars($item['alt_text']); ?>" class="w-full h-40 object-cover">
                <div class="p-4">
                  <div class="flex items-center">
                    <input type="checkbox" id="promo_<?php echo $item['id']; ?>" name="promoitems[]"
                      value="<?php echo $item['id']; ?>" class="h-5 w-5 text-red-500"
                      data-price="<?php echo $item['price']; ?>"
                      data-discounted-price="<?php echo $item['discounted_price']; ?>" onchange="calculateTotal()">
                    <label for="promo_<?php echo $item['id']; ?>" class="ml-2 text-gray-700">
                      <?php echo htmlspecialchars($item['name']); ?> -
                      <span style="text-decoration: line-through; color: #b0b0b0;">
                        ₱<?php echo number_format($item['price'], 2); ?>
                      </span>
                      <span style="color: #ff0000;">
                        ₱<?php echo number_format($item['discounted_price'], 2); ?>
                      </span>
                    </label>

                    <input type="number" name="promo_quantities[]" min="1" value="1"
                      class="ml-2 w-16 p-2 border rounded-md" onchange="calculateTotal()">
                  </div>
                </div>
              </div>
            <?php endforeach; ?>

            <?php
            foreach ($menu_items as $item):
              ?>
              <div class="card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>"
                  alt="<?php echo htmlspecialchars($item['alt_text']); ?>" class="w-full h-40 object-cover">
                <div class="p-4">
                  <div class="flex items-center">
                    <input type="checkbox" id="menu_<?php echo $item['id']; ?>" name="menu_items[]"
                      value="<?php echo $item['id']; ?>" class="h-5 w-5 text-red-500"
                      data-price="<?php echo $item['price']; ?>" onchange="calculateTotal()">
                    <label for="menu_<?php echo $item['id']; ?>"
                      class="ml-2 text-gray-700"><?php echo htmlspecialchars($item['name']); ?> -
                      ₱<?php echo number_format($item['price'], 2); ?></label>
                    <input type="number" name="menu_quantities[]" min="1" value="1"
                      class="ml-2 w-16 p-2 border rounded-md" onchange="calculateTotal()">
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Reservation Form Section -->
        <div class="w-full md:w-1/2 space-y-6 pl-6">
          <h2 class="text-3xl font-bold text-gray-800 text-center">Reserve a Table</h2>
          <p class="text-gray-600 mt-4 text-center">Fill out the form below to book your table at Victoria Grill
            Restaurant.</p>

          <!-- Reservation Date -->
          <div>
            <label for="date" class="block text-sm font-medium text-gray-700">Reservation Date</label>
            <!-- Date Input with JavaScript to Set Min Attribute -->
            <input type="date" id="date" name="date" required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
          </div>

          <!-- Time -->
          <div>
            <label for="time" class="block text-sm font-medium text-gray-700">Reservation Time</label>
            <input type="time" id="time" name="time" required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
          </div>

          <!-- Number of Guests -->
          <div>
            <label for="guests" class="block text-sm font-medium text-gray-700">Number of Guests</label>
            <select id="guests" name="guests" required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
              <option value="" disabled selected>Select number of guests</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8+</option>
            </select>
          </div>

          <!-- Reservation Type -->
          <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Reservation Type</label>
            <select id="type" name="type" required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
              onchange="toggleTakeOutOptions()">
              <option value="" disabled selected>Select type</option>
              <option value="Dine-In">Dine-In</option>
              <option value="TakeOut">Take-Out</option>
            </select>
          </div>

          <!-- Take-Out Options (Hidden by Default) -->
          <div id="takeOutOptions" class="hidden">
            <label for="takeoutType" class="block text-sm font-medium text-gray-700">Take-Out Option</label>
            <select id="takeoutType" name="takeoutType"
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
              onchange="toggleLocationField()">
              <option value="" disabled selected>Select option</option>
              <option value="Delivery">Delivery</option>
              <option value="Pick-Up">Pick-Up</option>
            </select>
          </div>

          <!-- Location (Hidden by Default) -->
          <div id="locationField" class="hidden">
            <label for="location" class="block text-sm font-medium text-gray-700">Delivery Location</label>
            <input type="text" id="location" name="location" placeholder="Enter your delivery location"
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
          </div>

          <!-- Payment Method -->
          <div>
            <label for="payment" class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select id="payment" name="payment" required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
              onchange="togglePaymentDetails()">
              <option value="" disabled selected>Select payment method</option>
              <option value="Visa">Visa</option>
              <option value="Gcash">Gcash</option>
            </select>
          </div>

          <?php
          // Assuming you have a connection to the database ($conn)
          
          // Fetch the payment details for Visa and GCash from the database
          $query = "SELECT * FROM payment_details WHERE payment_method = 'Visa' OR payment_method = 'Gcash'";
          $result = mysqli_query($conn, $query);

          $gcashDetails = null;
          $visaDetails = null;

          while ($row = mysqli_fetch_assoc($result)) {
            if ($row['payment_method'] == 'Visa') {
              $visaDetails = $row;
            } elseif ($row['payment_method'] == 'Gcash') {
              $gcashDetails = $row;
            }
          }
          ?>
          <!-- Payment Details -->
          <div id="paymentDetails" class="hidden">
            <!-- Gcash Details -->
            <div id="gcashDetails" class="hidden">
              <?php if ($gcashDetails): ?>
                <div class="flex items-center mt-2">

                  <img src="uploads/<?php echo $gcashDetails['payment_picture']; ?>" alt="Gcash QR Code" class="w-32 h-32 mr-4">
                  <p class="text-gray-700">Gcash: <br><strong>[<?php echo $gcashDetails['phone_number']; ?>]</strong></p>
                </div>
              <?php else: ?>
                <div class="flex items-center mt-2">
                  <img src="path/to/default-gcash-qr.png" alt="Gcash QR Code" class="w-32 h-32 mr-4">
                  <p class="text-gray-700">Gcash:<br> <strong>Not available</strong></p>
                </div>
              <?php endif; ?>
            </div>

            <!-- Visa Details -->
            <div id="visaDetails" class="hidden">
              <?php if ($visaDetails): ?>
                <div class="flex items-center mt-2">
                  <img src="uploads/<?php echo $visaDetails['payment_picture']; ?>" alt="Visa QR Code" class="w-32 h-32 mr-4">
                  <p class="text-gray-700">Visa Card Number:<br>
                    <strong>[<?php echo $visaDetails['phone_number']; ?>]</strong>
                  </p>
                </div>
              <?php else: ?>
                <div class="flex items-center mt-2">
                  <img src="path/to/default-visa-qr.png" alt="Visa QR Code" class="w-32 h-32 mr-4">
                  <p class="text-gray-700">Visa Card Number:<br> <strong>Not available</strong></p>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Payment Screenshot -->
          <div id="paymentScreenshotField" class="hidden">
            <p class="text-sm text-gray-500 mt-2">Note: At least 50% of the payment should be paid before the admin
              allows approval of the reservation.</p>
            <div class="bg-gray-100 p-3 rounded-lg shadow-md ">
              <h5 class="font-bold text-gray-800">Total: ₱<span id="alltotal">0.00</span></h5>
              <h5 class="font-bold text-gray-800">50% of Total: ₱<span id="halfTotal">0.00</span></h5>
            </div>
            <label for="payment_screenshot" class="block text-sm font-medium text-gray-700 mt-4">Payment
              Screenshot</label>
            <input type="file" id="payment_screenshot" name="payment_screenshot" accept="image/*" required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
          </div>

          <!-- Special Requests -->
          <div>
            <label for="requests" class="block text-sm font-medium text-gray-700">Special Requests</label>
            <textarea id="requests" name="requests" rows="4" placeholder="Enter any special requests (optional)"
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"></textarea>
          </div>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit" class="bg-red-800 hover:bg-red-600 text-white px-6 py-3 rounded-lg text-lg">Submit
              Reservation</button>
          </div>
        </div>
      </form>
    </div>
  </section>

  <script>
    // Function to toggle the visibility of the Take-Out Options
    function toggleTakeOutOptions() {
      const type = document.getElementById('type').value;
      const takeOutOptions = document.getElementById('takeOutOptions');
      if (type === 'TakeOut') {
        takeOutOptions.classList.remove('hidden');
      } else {
        takeOutOptions.classList.add('hidden');
        document.getElementById('locationField').classList.add('hidden');
      }
    }

    // Function to toggle the visibility of the Location Field
    function toggleLocationField() {
      const takeoutType = document.getElementById('takeoutType').value;
      const locationField = document.getElementById('locationField');
      if (takeoutType === 'Delivery') {
        locationField.classList.remove('hidden');
      } else {
        locationField.classList.add('hidden');
      }
    }

    // Function to toggle the visibility of the Payment Details and Screenshot Field
    function togglePaymentDetails() {
      const payment = document.getElementById('payment').value;
      const paymentDetails = document.getElementById('paymentDetails');
      const gcashDetails = document.getElementById('gcashDetails');
      const visaDetails = document.getElementById('visaDetails');
      const paymentScreenshotField = document.getElementById('paymentScreenshotField');

      paymentDetails.classList.remove('hidden');
      paymentScreenshotField.classList.remove('hidden');

      if (payment === 'Gcash') {
        gcashDetails.classList.remove('hidden');
        visaDetails.classList.add('hidden');
      } else if (payment === 'Visa') {
        visaDetails.classList.remove('hidden');
        gcashDetails.classList.add('hidden');
      } else {
        paymentDetails.classList.add('hidden');
        paymentScreenshotField.classList.add('hidden');
      }
    }

    // Function to validate the form
    function validateForm() {
      const selectedDate = new Date(document.getElementById('date').value);
      const today = new Date();

      // Clear the time part for accurate comparison
      today.setHours(0, 0, 0, 0);
      selectedDate.setHours(0, 0, 0, 0);

      if (selectedDate < today) {
        alert('Please select a date that is today or in the future.');
        return false;
      }

      const menuItems = document.querySelectorAll('input[name="menu_items[]"]:checked');
      const promoItems = document.querySelectorAll('input[name="promoitems[]"]:checked');
      if (menuItems.length === 0 && promoItems.length === 0) {
        alert('Please select at least one menu or promo item.');
        return false;
      }

      const payment = document.getElementById('payment').value;
      const paymentScreenshot = document.getElementById('payment_screenshot').value;
      if (payment && !paymentScreenshot) {
        alert('Please upload a payment screenshot.');
        return false;
      }

      return true;
    }

    // Function to calculate the total price
    function calculateTotal() {
      let total = 0;
      const menuItems = document.querySelectorAll('input[name="menu_items[]"]:checked');
      const promoItems = document.querySelectorAll('input[name="promoitems[]"]:checked');

      // Calculate regular menu items
      menuItems.forEach((item, index) => {
        const price = parseFloat(item.getAttribute('data-price'));
        const quantity = parseInt(document.querySelectorAll('input[name="menu_quantities[]"]')[index].value);
        total += price * quantity;
      });

      // Calculate promo items using discounted price
      promoItems.forEach((item, index) => {
        const discountedPrice = parseFloat(item.getAttribute('data-discounted-price'));
        const quantity = parseInt(document.querySelectorAll('input[name="promo_quantities[]"]')[index].value);
        total += discountedPrice * quantity;
      });

      const halfTotal = total / 2;

      document.getElementById('totalPrice').innerText = total.toFixed(2);
      document.getElementById('alltotal').innerText = total.toFixed(2);
      document.getElementById('halfTotal').innerText = halfTotal.toFixed(2);
    }
    // Call setMinDate on page load
    setMinDate();
  </script>
</body>

</html>