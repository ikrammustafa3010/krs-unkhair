<?php
    include "../../service/database.php";
    session_start();

    // Logout handler
    if (isset($_POST["logout"])) {
        session_unset();
        session_destroy();
        header("location: ../login.php"); // Sesuaikan path dengan struktur Anda
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
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .menu-box:active {
            transform: translateY(2px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <!-- Sidebar -->
    <style>
        /* Efek hover untuk tombol di sidebar */
        nav a {
            transition: background-color 0.3s, transform 0.2s;
        }

        nav a:hover {
            background-color: #1D4ED8; /* Ubah warna saat dihover */
            transform: translateX(10px); /* Efek geser ke kanan */
        }

        /* Efek saat tombol aktif (ketika ditekan atau dipilih) */
        nav a:active {
            background-color: #2563EB; /* Warna saat tombol aktif */
            transform: scale(0.98); /* Efek menekan tombol */
        }

        /* Efek saat tombol difokuskan */
        nav a:focus {
            outline: none; /* Menghapus outline default */
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.5); /* Menambahkan shadow biru */
        }

        /* Mengubah warna tombol yang aktif (menunjukkan halaman yang sedang dibuka) */
        nav a.bg-blue-700 {
            background-color: #3B82F6; /* Warna aktif tombol (untuk tombol Profile) */
        }

        /* Mengubah warna tombol logout saat dihover */
        nav button:hover {
            background-color: #1D4ED8; /* Ubah warna saat tombol logout dihover */
            transform: translateX(10px); /* Efek geser ke kanan */
        }
    </style>
    <div class="flex">
        <div class="bg-blue-600 w-64 min-h-screen text-white p-4">
            <!-- Logo -->
            <div class="flex items-center mb-8">
                <img src="logo.png" alt="logo" class="w-12 h-12 rounded-full mr-3">
                <div>
                    <h2 class="font-bold">KRS Unkhair</h2>
                    <p class="text-sm text-blue-200">Sistem Manajemen KRS</p>
                </div>
            </div>
            <nav>
                <a href="dashboardAdmin.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-home mr-2"></i> Beranda
                </a>
                <a href="mataKuliah.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-book mr-2"></i> Mata Kuliah
                </a>
                <a href="dataMahasiswa.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-users mr-2"></i> Data Mahasiswa
                </a>
                <a href="penentuanMatakuliah.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-cogs mr-2"></i> Prasyarat Mata Kuliah
                </a>
                <!-- Tombol Logout -->
                <form method="POST" action="">
                    <button type="submit" name="logout" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center w-full text-left text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Beranda Section -->
            <section id="beranda" class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl font-semibold mb-4">Selamat Datang di Dashboard</h2>
                <p class="text-gray-600 mb-6">Pilih menu berikut untuk mengelola data KRS mahasiswa.</p>
                <div class="grid grid-cols-3 gap-6">
                    <!-- Menu Kotak Beranda -->
                    <a href="dashboardAdmin.php" class="menu-box bg-blue-500 text-white p-6 rounded-lg shadow-lg text-center">
                        <i class="fas fa-home text-4xl mb-4"></i>
                        <h3 class="font-semibold">Beranda</h3>
                    </a>
                    <a href="mataKuliah.php" class="menu-box bg-green-500 text-white p-6 rounded-lg shadow-lg text-center">
                        <i class="fas fa-book text-4xl mb-4"></i>
                        <h3 class="font-semibold">Mata Kuliah</h3>
                    </a>
                    <a href="dataMahasiswa.php" class="menu-box bg-yellow-500 text-white p-6 rounded-lg shadow-lg text-center">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <h3 class="font-semibold">Data Mahasiswa</h3>
                    </a>
                    <a href="penentuanMatakuliah.php" class="menu-box bg-indigo-500 text-white p-6 rounded-lg shadow-lg text-center">
                        <i class="fas fa-cogs text-4xl mb-4"></i>
                        <h3 class="font-semibold">Prasyarat Mata Kuliah</h3>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <footer class="bg-gray-800 text-white p-4 text-center mt-6">
        <p>&copy; 2024 Sistem Manajemen KRS | Universitas Khairun</p>
    </footer>
</body>
</html>
