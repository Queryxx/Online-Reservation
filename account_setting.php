<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user details
include 'conn.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();
$conn->close();

// Set profile picture in session
$_SESSION['profile_picture'] = $profile_picture;

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $filename = uniqid() . '_' . basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        include 'conn.php';
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        $_SESSION['profile_picture'] = $target_file;
        header("Location: userdashboard.php");
        exit();
    } else {
        echo 'Error uploading file.';
    }
}

// Handle profile picture removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_picture'])) {
    include 'conn.php';
    $stmt = $conn->prepare("UPDATE users SET profile_picture = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $_SESSION['profile_picture'] = 'default.jpg';
    header("Location: userdashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'], $_POST['pin'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $enteredPin = $_POST['pin'];

    // Validate PIN
    if ($enteredPin != $_SESSION['pin']) {
        die('Invalid PIN.');
    }

    if ($newPassword === $confirmPassword) {
        // Hash the new password and update in database
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        include 'conn.php';
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param('si', $hashedPassword, $_SESSION['user_id']);
        if ($stmt->execute()) {
            echo '<script>alert("Password changed successfully.");</script>';
        } else {
            echo 'Error changing password.';
        }
        $stmt->close();
        $conn->close();
    } else {
        echo 'Passwords do not match.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php include 'user_nav.php'; ?>
</head>

<body>
    <main class="main-content p-6 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md">
            <!-- Change Password Section -->
            <div class="p-6 border-b border-gray-300">
                <h2 class="text-2xl font-semibold text-gray-800">Security Settings</h2>
            </div>

            <div class="p-6 space-y-8">
                <!-- Change Password Form -->
                <form id="change-password-form" method="POST" action="" class="space-y-6">
                    <!-- Current Password Input -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password:</label>
                        <input type="password" id="current_password" name="current_password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- New Password Input -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password:</label>
                        <input type="password" id="new_password" name="new_password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- Confirm Password Input -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- Send PIN Button -->
                    <div>
                        <button type="button" id="send-pin-button"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i> Send PIN
                        </button>
                    </div>

                    <!-- PIN Input -->
                    <div id="pin-section" style="display:none;">
                        <label for="pin" class="block text-sm font-medium text-gray-700">Enter PIN:</label>
                        <input type="text" id="pin" name="pin"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                            <i class="fas fa-key mr-2"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>

            <script>
                document.getElementById('send-pin-button').addEventListener('click', function() {
                    const currentPassword = document.getElementById('current_password').value;

                    fetch('validate_password.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                current_password: currentPassword
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Proceed to send the PIN
                                fetch('send_pin.php', {
                                        method: 'POST',
                                        body: new URLSearchParams(new FormData(document.getElementById('change-password-form')))
                                    })
                                    .then(response => response.json())
                                    .then(pinData => {
                                        if (pinData.success) {
                                            document.getElementById('pin-section').style.display = 'block';
                                        }
                                    });
                            } else {
                                alert('Current password is incorrect.');
                            }
                        });
                });

                function requestDeletePin() {
                    document.getElementById('delete_form').style.display = 'block';
                }
            </script>
    </main>

    <!-- Profile Picture Modal -->
    <div id="profile-menu" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50" id="modal-backdrop"></div>

        <!-- Modal Content -->
        <div
            class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-xl p-6 w-96 max-w-[90%]">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Profile Picture</h3>
                <button class="text-gray-400 hover:text-gray-600" id="close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Current Profile Picture -->
            <div class="flex justify-center mb-6">
                <img src="<?= htmlspecialchars($profile_picture ? $profile_picture : 'default.jpg'); ?>"
                    alt="Profile Picture"
                    class="w-32 h-32 profile-picture rounded-full object-cover border-2 border-gray-300 hover:border-blue-500"
                    id="profile-picture">
            </div>

            <!-- Upload Form -->
            <form action="userdashboard.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Update Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Upload New Picture
                </button>
            </form>

            <!-- Remove Picture Form -->
            <form action="userdashboard.php" method="POST" class="mt-4">
                <button type="submit" name="remove_picture"
                    class="w-full bg-red-50 text-red-600 py-2 px-4 rounded-lg hover:bg-red-100 transition-colors">
                    Remove Picture
                </button>
            </form>
        </div>
    </div>
    <script>
        // Clean consolidated JavaScript
        const profilePicture = document.getElementById('profile-picture');
        const profileMenu = document.getElementById('profile-menu');
        const closeModal = document.getElementById('close-modal');
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        function openModal() {
            profileMenu.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalAndReset() {
            profileMenu.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            backdrop.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        // Event Listeners
        profilePicture.addEventListener('click', (e) => {
            e.stopPropagation();
            openModal();
        });

        closeModal.addEventListener('click', closeModalAndReset);

        profileMenu.addEventListener('click', (e) => {
            if (e.target === profileMenu) {
                closeModalAndReset();
            }
        });

        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });

        backdrop.addEventListener('click', toggleSidebar);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModalAndReset();
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>

</html>