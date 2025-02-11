<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Assuming you have a function to get user details
include 'conn.php';
$user_id = $_SESSION['user_id'];
// Modify the SQL query to include the mobile number
$stmt = $conn->prepare("SELECT name, email, profile_picture, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture, $mobile_number);
$stmt->fetch();
$stmt->close();
$conn->close();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    // Ensure the uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        // Update profile picture in database
        include 'conn.php';
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        if ($stmt->execute()) {
            $_SESSION['profile_picture'] = $target_file;
            echo "The file " . htmlspecialchars(basename($_FILES["profile_picture"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error updating your profile picture.";
        }
        $stmt->close();
        $conn->close();

        // Refresh the page to show the new profile picture
        header("Location: profile.php");
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Handle profile picture removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_picture'])) {
    // Update profile picture in database to null
    include 'conn.php';
    $stmt = $conn->prepare("UPDATE users SET profile_picture = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $_SESSION['profile_picture'] = 'default.jpg';
        echo "Profile picture has been removed.";
    } else {
        echo "Sorry, there was an error removing your profile picture.";
    }
    $stmt->close();
    $conn->close();

    // Refresh the page to show the default profile picture
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Ensure smooth transition for sidebar on mobile toggle */
        .sidebar {
            transition: transform 0.3s ease;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
        }
    </style>
</head>
<body class="font-sans bg-gray-100">

    <!-- Sidebar (for larger screens) -->
    <div id="sidebar" class="sidebar w-64 bg-blue-900 text-white h-screen p-6 hidden md:block fixed top-0 left-0 z-50">
        <!-- Profile Section -->
        <div class="flex items-center mb-6">
            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture"
                class="w-12 h-12 rounded-full mr-4 cursor-pointer" onclick="openModal()">
            <div>
                <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($name); ?></h3>
                <p class="text-sm text-gray-400"><?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>
        <!-- Navigation Links -->
        <ul>
            <li><a href="index.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-home mr-2"></i>Home</a></li>
            <li><a href="profile.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-user mr-2"></i>Profile</a></li>
            <li><a href="orders.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-box mr-2"></i>Orders</a></li>
            <li><a href="messages.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-envelope mr-2"></i>Messages</a></li>
            <li><a href="settings.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-cog mr-2"></i>Settings</a></li>
            <li><a href="logout.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Sidebar (for smaller screens) -->
    <div id="sidebar-mobile"
        class="sidebar-mobile w-64 bg-blue-900 text-white h-screen p-6 fixed inset-0 transform -translate-x-full md:hidden z-50">
        <!-- Profile Section -->
        <div class="flex items-center mb-6">
            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>"
                alt="Profile Picture" class="w-12 h-12 rounded-full mr-4 cursor-pointer" onclick="openModal()">
            <div>
                <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($name); ?></h3>
                <p class="text-sm text-gray-400">Client</p>
            </div>
        </div>
        <!-- Navigation Links -->
        <ul>
            <li><a href="index.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-home mr-2"></i>Home</a></li>
            <li><a href="profile.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-user mr-2"></i>Profile</a></li>
            <li><a href="orders.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-box mr-2"></i>Orders</a></li>
            <li><a href="messages.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-envelope mr-2"></i>Messages</a></li>
            <li><a href="settings.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-cog mr-2"></i>Settings</a></li>
            <li><a href="logout.php" class="block py-2 px-4 hover:bg-blue-700"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div class="flex md:flex-row flex-col-reverse min-h-screen ml-0 md:ml-64">
        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl text-gray-900">Profile</h1>
                    <p class="text-gray-600">Manage your profile information.</p>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Profile Information</h3>
                <div class="mb-4">
                    <label class="block text-gray-700">Name</label>
                    <p class="text-gray-900"><?php echo htmlspecialchars($name); ?></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Email</label>
                    <p class="text-gray-900"><?php echo htmlspecialchars($email); ?></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Mobile Number</label>
                    <p class="text-gray-900"><?php echo htmlspecialchars($mobile_number); ?></p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Profile Picture</label>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full">
                </div>
                <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="openModal()">Change Profile Picture</button>
            </div>
        </div>
    </div>

    <!-- Profile Picture Modal -->
    <div id="profile-picture-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Change Profile Picture</h3>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" required class="mb-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
            </form>
            <form action="profile.php" method="POST">
                <input type="hidden" name="remove_picture" value="1">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Remove</button>
            </form>
        </div>
    </div>
    <script src="script/modalpp.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>