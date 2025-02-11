<?php include "conn.php";
session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Victoria Grill Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-red-800 text-white py-4 relative">
        <div class="container mx-auto flex justify-center items-center">
            <div class="absolute left-4 top-10">
                <a href="javascript:history.back()" class="text-white hover:text-gray-400">
                    <i class="fas fa-arrow-left text-2xl"></i>
                </a>
            </div>
            <div class="text-center">
                <img src="victoria.jpg" alt="Victoria Grill Logo" class="h-12 w-12 mx-auto rounded-full mb-2">
                <h1 class="text-2xl font-bold">Victoria Grill Restaurant</h1>
            </div>
        </div>
    </header>
    <main class="container mx-auto p-8 py-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-3xl font-bold mb-4">Terms of Service</h2>
            <p class="mb-4">Welcome to Victoria Grill Restaurant. These terms and conditions outline the rules and regulations for the use of our website.</p>
            
            <h3 class="text-2xl font-semibold mb-2">1. Terms</h3>
            <p class="mb-4">By accessing this website, you agree to be bound by these terms of service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws.</p>
            
            <h3 class="text-2xl font-semibold mb-2">2. Use License</h3>
            <p class="mb-4">Permission is granted to temporarily download one copy of the materials (information or software) on Victoria Grill Restaurant's website for personal, non-commercial transitory viewing only.</p>
            
            <h3 class="text-2xl font-semibold mb-2">3. Disclaimer</h3>
            <p class="mb-4">The materials on Victoria Grill Restaurant's website are provided on an 'as is' basis. Victoria Grill Restaurant makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties.</p>
            
            <h3 class="text-2xl font-semibold mb-2">4. Limitations</h3>
            <p class="mb-4">In no event shall Victoria Grill Restaurant or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Victoria Grill Restaurant's website.</p>
            
            <h3 class="text-2xl font-semibold mb-2">5. Revisions and Errata</h3>
            <p class="mb-4">The materials appearing on Victoria Grill Restaurant's website could include technical, typographical, or photographic errors. Victoria Grill Restaurant does not warrant that any of the materials on its website are accurate, complete, or current.</p>
            
            <h3 class="text-2xl font-semibold mb-2">6. Links</h3>
            <p class="mb-4">Victoria Grill Restaurant has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site.</p>
            
            <h3 class="text-2xl font-semibold mb-2">7. Site Terms of Use Modifications</h3>
            <p class="mb-4">Victoria Grill Restaurant may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p>
            
            <h3 class="text-2xl font-semibold mb-2">8. Governing Law</h3>
            <p class="mb-4">These terms and conditions are governed by and construed in accordance with the laws of the state and you irrevocably submit to the exclusive jurisdiction of the courts in that state or location.</p>
        </div>
    </main>
    <footer class="bg-red-800 text-white py-6">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 Victoria Grill Restaurant. All rights reserved.</p>
            <div class="mt-4 space-x-6">
                <a href="privacy-policy.php" class="text-white hover:text-gray-400 text-sm">Privacy Policy</a> |
                <a href="terms-of-service.php" class="text-white hover:text-gray-400 text-sm">Terms of Service</a>
            </div>
            <div class="mt-4">
                <a href="https://www.facebook.com/victoriagrillandrestaurant" target="_blank" class="text-white hover:text-gray-400 mx-3">
                    <i class="fab fa-facebook-square text-xl"></i>
                </a>
                <a href="https://www.instagram.com/victoriagrillandrestaurant" target="_blank" class="text-white hover:text-gray-400 mx-3">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <a href="https://www.tiktok.com/victoriagrillandrestaurant" target="_blank" class="text-white hover:text-gray-400 mx-3">
                    <i class="fab fa-tiktok text-xl"></i>
                </a>
            </div>
        </div>
    </footer>
</body>
</html>