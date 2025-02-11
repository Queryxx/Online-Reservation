<?php
session_start();

// Clear session variables
session_unset();
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
?>
