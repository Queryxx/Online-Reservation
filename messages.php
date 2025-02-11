<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include 'conn.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch user's messages only
$query = "SELECT message_id, user_id, message, created_at 
          FROM contact_messages 
          WHERE user_id = ? 
          ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch replies for user's messages
$replies_query = "SELECT contact_id, reply_message, created_at, admin_id 
                 FROM contact_replies 
                 WHERE contact_id IN (SELECT message_id FROM contact_messages WHERE user_id = ?) 
                 ORDER BY created_at ASC";
$stmt = $conn->prepare($replies_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$replies_result = $stmt->get_result();

$replies = [];
while ($reply_row = $replies_result->fetch_assoc()) {
    $replies[$reply_row['contact_id']][] = $reply_row;
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
    <meta name="description" content="User Messages - Victoria Grill Restaurant">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>User Messages</title>

</head>

<body class="bg-gradient-to-r font-sans">
    <?php include 'user_nav.php'; ?>

    <!-- Main Content -->
    <div class="main-content p-6">
        <h1 class="mb-4 text-2xl font-bold">My Messages</h1>

        <?php if ($message): ?>
            <div class="alert alert-info mb-4 p-4 bg-blue-100 text-blue-700 rounded"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <!-- Message Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">You sent:</span>
                            </div>
                            <time class="text-sm text-gray-500">
                                <?php echo date('F j, Y g:i A', strtotime($row['created_at'])); ?>
                            </time>
                        </div>

                        <!-- Message Content -->
                        <div class="text-gray-700 text-lg mb-4 border-l-4 border-blue-200 pl-4">
                            <?php echo htmlspecialchars($row['message']); ?>
                        </div>

                        <!-- Replies Section -->
                        <?php if (isset($replies[$row['message_id']])): ?>
                            <div class="mt-6 space-y-4">
                                <h6 class="font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-reply text-gray-400"></i> Replies
                                </h6>
                                <?php foreach ($replies[$row['message_id']] as $reply): ?>
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <div class="flex items-center gap-2 mb-2">
                                            <?php if ($reply['admin_id']): ?>
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Admin</span>
                                            <?php endif; ?>
                                            <time class="text-xs text-gray-500">
                                                <?php echo date('F j, Y g:i A', strtotime($reply['created_at'])); ?>
                                            </time>
                                        </div>
                                        <p class="text-gray-700">
                                            <?php echo htmlspecialchars($reply['reply_message']); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info p-4 bg-blue-100 text-blue-700 rounded">No messages found.</div>
        <?php endif; ?>
    </div>
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
    <script>
        <?php if ($message): ?>
            alert('<?php echo $message; ?>');
        <?php endif; ?>
    </script>
</body>

</html>