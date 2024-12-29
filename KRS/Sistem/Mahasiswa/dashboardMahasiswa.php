<?php
include "../../service/database.php";
session_start();

// Logout handler
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php"); // Adjust path based on your structure
    exit();
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen KRS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <style>
        .menu-box {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .menu-box:hover {
            transform: translateY(-5px); /* Lift the box */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Shadow effect */
        }
        .menu-box:active {
            transform: translateY(2px); /* Press the box */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        /* Sidebar button effects */
        nav a {
            transition: background-color 0.3s, transform 0.2s;
        }
        nav a:hover {
            background-color: #1D4ED8; /* Hover color */
            transform: translateX(10px); /* Slide effect */
        }
        nav a:active {
            background-color: #2563EB; /* Active button color */
            transform: scale(0.98); /* Button press effect */
        }
        nav a:focus {
            outline: none; /* Remove default outline */
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.5); /* Blue shadow */
        }
        nav a.bg-blue-700 {
            background-color: #3B82F6; /* Active button background */
        }
        nav button:hover {
            background-color: #1D4ED8; /* Hover color for logout button */
            transform: translateX(10px); /* Slide effect */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <!-- Sidebar -->
    <div class="flex">
        <div class="bg-blue-600 w-64 min-h-screen text-white p-4">
            <!-- Logo -->
            <div class="flex items-center mb-8">
                <img src="logo.png" alt="Logo" class="w-13 h-12 rounded-full mr-3">
                <div>
                    <h2 class="font-bold">KRS Unkhair</h2>
                    <p class="text-sm text-blue-200">Sistem Manajemen KRS</p>
                </div>
            </div>
            <nav>
                <a href="dashboardMahasiswa.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-home mr-2"></i> Beranda
                </a>
                <a href="pengajuanKHS.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-edit mr-2"></i> Pengajuan KHS
                </a>
                <a href="profile.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <!-- Logout button -->
                <form method="POST" action="">
                    <button type="submit" name="logout" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center w-full text-left text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
            </nav>            
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Beranda Section (Kotak-kotak Menu) -->
            <section id="beranda" class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl font-semibold mb-4">Selamat Datang di Dashboard</h2>
                <p class="text-gray-600 mb-6">Pilih menu berikut untuk menentuka KRS Anda.</p>
                <div class="grid grid-cols-3 gap-6">
                    <!-- Menu Kotak Beranda -->
                    <div class="menu-box bg-blue-500 text-white p-6 rounded-lg shadow-lg text-center">
                        <a href="dashboardMahasiswa.php">
                            <i class="fas fa-home text-4xl mb-4"></i>
                            <h3 class="font-semibold">Beranda</h3>
                        </a>
                    </div>
                    
                    <div class="menu-box bg-green-500 text-white p-6 rounded-lg shadow-lg text-center">
                        <a href="pengajuanKHS.php">
                            <i class="fas fa-edit text-4xl mb-4"></i>
                            <h3 class="font-semibold">Pengajuan KHS</h3>
                        </a>
                    </div>
                    
                    <div class="menu-box bg-indigo-500 text-white p-6 rounded-lg shadow-lg text-center">
                        <a href="profile.php">
                            <i class="fas fa-user text-4xl mb-4"></i>
                            <h3 class="font-semibold">Profile</h3>
                        </a>
                    </div>                   
                </div>
            </section>

        </div>
    </div>

    <footer class="bg-gray-800 text-white p-4 text-center mt-6">
        <p>&copy; 2024 Sistem Manajemen KRS | Universitas Khairun</p>
    </footer>
</body>
</html>
