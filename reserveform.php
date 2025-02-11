<?php
session_start();
include 'conn.php'; // Assuming you have a connection to the database
require 'vendor/autoload.php'; // Include Composer's autoloader
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
    foreach ($menu_items_selected as $index => $menu_item_id) {
      $promo_item_id = null;
      $quantity = $menu_quantities[$index];
      $stmt_menu->bind_param("iiii", $reservation_id, $menu_item_id, $promo_item_id, $quantity);
      $stmt_menu->execute();
    }
    foreach ($promo_items_selected as $index => $promo_item_id) {
      $menu_item_id = null;
      $quantity = $promo_quantities[$index];
      $stmt_menu->bind_param("iiii", $reservation_id, $menu_item_id, $promo_item_id, $quantity);
      $stmt_menu->execute();
    }

    $stmt_menu->close();

    // Send confirmation email using PHPMailer
    $mail = new PHPMailer(true);
    try {
      // Server settings
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'victoriagrillrestaurant@gmail.com';
      $mail->Password = 'rwno bsje uwrx irqy';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      // Recipients
      $mail->setFrom('victoriagrillrestaurant@gmail.com', 'Victoria Grill Restaurant');
      $mail->addAddress($_SESSION['user_email']); // Add a recipient

      // Content
      $mail->isHTML(true);
      $mail->Subject = 'Reservation Submission Confirmation';
      $mail->Body = "
      Dear Valued Customer,<br><br>
      Thank you for your reservation request at Victoria Grill Restaurant. Your reservation ID is <strong>$reservation_id</strong>. 
      Your reservation has been submitted and is currently awaiting approval by our administration team. 
      Please note that payments made for reservations are non-refundable.<br><br>
      We will notify you once it has been confirmed.<br><br>
      If you have any further questions or need assistance, please do not hesitate to contact us.<br><br>
      Best regards,<br>
      Victoria Grill Restaurant
  ";


      $mail->send();
      // Redirect to a confirmation page or display a success message
      header("Location: reservation_success.php");
      exit();
    } catch (Exception $e) {
      echo "<script>
          alert('Please Login First');
          window.location.href = 'login.php';
      </script>";
      exit();
    }
  } else {
    echo "<script>alert('Error: Unable to process your reservation. Please try again later.');</script>";
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
  <style>
    /* Fade-in and slide-in from bottom animation */
    .fade-slide-in {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 1s ease-out, transform 1s ease-out;
    }

    .fade-slide-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .not-available {
      filter: grayscale(100%);
      opacity: 0.7;
      pointer-events: none;
      position: relative;
    }

    .not-available::after {
      content: 'Not Available';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.25rem;
      font-weight: bold;
    }

    .not-available input[type="checkbox"],
    .not-available input[type="number"] {
      pointer-events: none;
      opacity: 0.5;
    }
  </style>
</head>

<body class="bg-gray-100">
  <?php include 'reservenav.php'; ?>
  <!-- Reservation Form Section -->
  <section id="reservation" class="py-16 bg-white fade-slide-in">
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

          <!-- Category Tabs -->
          <div class="tabs mt-4 flex justify-center space-x-2 overflow-x-auto">
            <?php
            $categories = array('specials', 'vip', 'pasta', 'pica-pica', 'sweets', 'drinks');
            foreach ($categories as $category) : ?>
              <button type="button"
                onclick="filterByCategory('<?php echo $category; ?>')"
                class="category-tab px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-red-500 hover:text-white transition-colors">
                <?php echo ucfirst($category); ?>
              </button>
            <?php endforeach; ?>
          </div>

          <!-- Menu and Promo Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-8">
            <?php
            $count = 0;
            foreach ($promo_items as $item):
              $count++;
              $hiddenClass = $count > 9 ? 'hidden extra-item' : '';
            ?>
              <div class="card bg-white rounded-lg shadow-md overflow-hidden <?php echo $hiddenClass; ?>">
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

                    <input type="number"
                      name="promo_quantities[]"
                      min="1"
                      max="10"
                      value="1"
                      class="ml-2 w-16 p-2 border rounded-md"
                      onchange="validateQuantity(this); calculateTotal()">

                  </div>
                </div>
              </div>
            <?php endforeach; ?>


            <?php $count = 0;
            foreach ($menu_items as $item): $count++;
              $isNotAvailable = $item['status'] === 'not_available';
            ?>
              <div class="card bg-white rounded-lg shadow-md overflow-hidden <?php
                                                                              echo $count > 6 ? 'hidden extra-item' : '';
                                                                              echo $isNotAvailable ? ' not-available' : '';
                                                                              ?>">
                <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>"
                  alt="<?php echo htmlspecialchars($item['alt_text']); ?>"
                  class="w-full h-40 object-cover">
                <div class="p-4">
                  <div class="flex items-center">
                    <input type="checkbox"
                      id="menu_<?php echo $item['id']; ?>"
                      name="menu_items[]"
                      value="<?php echo $item['id']; ?>"
                      class="h-5 w-5 text-red-500"
                      data-price="<?php echo $item['price']; ?>"
                      data-category="<?php echo $item['category']; ?>"
                      onchange="calculateTotal()"
                      <?php echo $isNotAvailable ? 'disabled' : ''; ?>>
                    <label for="menu_<?php echo $item['id']; ?>"
                      class="ml-2 text-gray-700 <?php echo $isNotAvailable ? 'text-gray-400' : ''; ?>">
                      <?php echo htmlspecialchars($item['name']); ?> -
                      ₱<?php echo number_format($item['price'], 2); ?>
                    </label>
                    <input type="number"
                      name="menu_quantities[]"
                      min="1"
                      max="10"
                      value="1"
                      class="ml-2 w-16 p-2 border rounded-md"
                      onchange="validateQuantity(this); calculateTotal()"
                      <?php echo $isNotAvailable ? 'disabled' : ''; ?>>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <script>
          function validateQuantity(input) {
            if (input.value > 10) {
              alert('Maximum quantity allowed is 10');
              input.value = 10;
            }
            if (input.value < 1) {
              input.value = 1;
            }
          }

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

          // Show all items initially
          document.addEventListener('DOMContentLoaded', () => {
            const allCards = document.querySelectorAll('.card');
            allCards.forEach(card => card.style.display = 'block');
          });
        </script>

        <!-- Reservation Form Section -->
        <div class="w-full md:w-1/2 space-y-6 pl-6">
          <h2 class="text-3xl font-bold mt-5 text-gray-800 text-center">Reserve a Table</h2>
          <p class="text-gray-600 mt-4 text-center">Fill out the form below to book your table at Victoria Grill
            Restaurant.</p>

          <!-- Reservation Date -->
          <div>
            <label for="date" class="block text-sm font-medium text-gray-700">Reservation Date</label>
            <!-- Date Input with JavaScript to Set Min Attribute -->
            <input type="date" id="date" name="date" required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
          </div>

          <div>
            <label for="time" class="block text-sm font-medium text-gray-700">Reservation Time (8:00 AM - 5:00 PM)</label>
            <input type="time"
              id="time"
              name="time"
              min="08:00"
              max="17:00"
              required
              class="mt-1 p-4 block w-full border rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
              onchange="validateTime(this)">
            <p class="text-sm text-gray-500 mt-1">Business hours: 8:00 AM to 5:00 PM</p>
          </div>

          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const dateInput = document.getElementById('date');
              const timeInput = document.getElementById('time');

              // Set the minimum date to today
              const today = new Date().toISOString().split('T')[0];
              dateInput.setAttribute('min', today);

              // Function to update the minimum time
              function updateMinTime() {
                const selectedDate = new Date(dateInput.value);
                const now = new Date();
                const minTime = new Date(now.getTime() + 20 * 60000); // 20 minutes from now

                if (selectedDate.toDateString() === now.toDateString()) {
                  const hours = minTime.getHours().toString().padStart(2, '0');
                  const minutes = minTime.getMinutes().toString().padStart(2, '0');
                  timeInput.setAttribute('min', `${hours}:${minutes}`);
                } else {
                  timeInput.removeAttribute('min');
                }
              }

              // Update the minimum time when the date changes
              dateInput.addEventListener('change', updateMinTime);

              // Update the minimum time on page load
              updateMinTime();
            });
          </script>
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
            <div class="mt-1 p-4 block w-full border rounded-md bg-gray-50">
              <input type="hidden" name="payment" value="Gcash">
              <p class="text-gray-700">GCash Only</p>
            </div>
          </div>
          <?php
          // Assuming you have a connection to the database ($conn)

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
          <div id="gcashDetails">
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


          <!-- Payment Screenshot (always visible) -->
          <div id="paymentScreenshotField">
            <p class="text-sm text-gray-500 mt-2">Note: At least 50% of the payment should be paid before the admin allows approval of the reservation. Please note that this payment is non-refundable.</p>

            <div class="bg-gray-100 p-3 rounded-lg shadow-md">
              <h5 class="font-bold text-gray-800">Total: ₱<span id="alltotal">0.00</span></h5>
              <h5 class="font-bold text-gray-800">50% of Total: ₱<span id="halfTotal">0.00</span></h5>
            </div>
            <label for="payment_screenshot" class="block text-sm font-medium text-gray-700 mt-4">Payment Screenshot</label>
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


    function validateTime(input) {
      const selectedTime = input.value;
      const [hours, minutes] = selectedTime.split(':').map(Number);
      const totalMinutes = hours * 60 + minutes;

      if (totalMinutes < 8 * 60 || totalMinutes > 17 * 60) {
        input.setCustomValidity('Please select a time between 8:00 AM and 5:00 PM');
        input.reportValidity();
        return false;
      }

      input.setCustomValidity('');
      return true;
    }


    // Function to validate the form
    function validateForm() {
      const selectedDate = new Date(document.getElementById('date').value);
      const selectedTime = document.getElementById('time').value;
      const [hours, minutes] = selectedTime.split(':').map(Number);

      // Get current Philippine time
      const now = new Date(new Date().toLocaleString('en-US', {
        timeZone: 'Asia/Manila'
      }));

      // Create a date object with selected date and time
      const reservationDateTime = new Date(selectedDate);
      reservationDateTime.setHours(hours, minutes, 0, 0);

      // Add 20 minutes buffer to current time
      const bufferTime = new Date(now.getTime() + 20 * 60000);

      if (reservationDateTime < bufferTime) {
        alert('Please select a future time with at least 20 minutes advance notice.');
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

      // Improved time validation

      const timeInput = document.getElementById('time');
      if (!validateTime(timeInput)) {
        return false;
      }

      return true;
    }



    // Function to calculate the total price
    function calculateTotal() {
      let total = 0;

      // Calculate total for promo items
      document.querySelectorAll('input[name="promoitems[]"]').forEach((checkbox, index) => {
        if (checkbox.checked) {
          const price = parseFloat(checkbox.dataset.discountedPrice);
          const quantity = parseInt(document.querySelectorAll('input[name="promo_quantities[]"]')[index].value);
          total += price * quantity;
        }
      });

      // Calculate total for menu items
      document.querySelectorAll('input[name="menu_items[]"]').forEach((checkbox, index) => {
        if (checkbox.checked) {
          const price = parseFloat(checkbox.dataset.price);
          const quantity = parseInt(document.querySelectorAll('input[name="menu_quantities[]"]')[index].value);
          total += price * quantity;
        }
      });

      const halfTotal = total / 2;

      document.getElementById('totalPrice').innerText = total.toFixed(2);
      document.getElementById('alltotal').innerText = total.toFixed(2);
      document.getElementById('halfTotal').innerText = halfTotal.toFixed(2);
    }
    // Call setMinDate on page load
    setMinDate();
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const fadeSlideInElement = document.querySelector('#reservation');
      fadeSlideInElement.classList.add('visible');
    });
  </script>
</body>

</html>