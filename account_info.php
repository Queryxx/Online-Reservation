<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user details
include 'conn.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email,phone, profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email,$phone, $profile_picture);
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
    <main class="main-content p-6">
        <!-- Account Information -->
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-semibold text-gray-800">Account Information</h2>
            </div>

            <div class="p-6">
                <form action="update_account.php" method="POST" class="space-y-6">
                    <!-- Personal Information Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>"
                                    class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 bg-gray-50 text-gray-600">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <div
                                    class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 bg-red-50 text-gray-600">
                                    <?= htmlspecialchars($email) ?>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <div
                                    class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 bg-red-50 text-gray-600">
                                    <?= htmlspecialchars($phone) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Picture section (View Only) -->
                    <div class="space-y-4 pt-6">
                        <h3 class="text-lg font-medium text-gray-900">Profile Picture</h3>

                        <div class="flex items-center space-x-8">
                            <div class="flex flex-col items-center space-y-2">
                                <img src="<?= htmlspecialchars($profile_picture ? $profile_picture : 'default.jpg'); ?>"
                                    alt="Profile Picture"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                                <span class="text-sm text-gray-600">Current Photo</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            Update Name
                        </button>
                    </div>
                </form>
            </div>
        </div>
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