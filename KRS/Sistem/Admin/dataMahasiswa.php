<?php
include "../../service/database.php"; // Make sure the path is correct
session_start();

// Logout handler
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php"); // Adjust the path based on your structure
    exit();
}

// Process adding a new student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addMahasiswa"])) {
    $npm_mhs = $_POST["npm_mhs"];
    $nama_mhs = $_POST["nama_mhs"];
    $semester_mhs = $_POST["semester_mhs"];
    $username_mhs = $_POST["username_mhs"];
    $password_mhs = password_hash($_POST["password_mhs"], PASSWORD_BCRYPT); // Hashing password

    if ($npm_mhs && $nama_mhs && $semester_mhs && $username_mhs && $password_mhs) {
        $query = "INSERT INTO data_mhs (npm_mhs, nama_mhs, semester_mhs, username_mhs, password_mhs) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ssiss", $npm_mhs, $nama_mhs, $semester_mhs, $username_mhs, $password_mhs);
            if ($stmt->execute()) {
                $successMsg = "Mahasiswa berhasil ditambahkan.";
            } else {
                $errorMsg = "Gagal menambahkan mahasiswa: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMsg = "Kesalahan pada query: " . $db->error;
        }
    } else {
        $errorMsg = "Harap lengkapi semua field yang wajib diisi!";
    }
}

// Proses Hapus Mahasiswa
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteMahasiswa"])) {
    $delete_id = $_POST["delete_id"];

    if ($delete_id) {
        $query = "DELETE FROM data_mhs WHERE npm_mhs = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $delete_id);

        if ($stmt->execute()) {
            $successMsg = "Mahasiswa berhasil dihapus.";
        } else {
            $errorMsg = "Gagal menghapus mahasiswa: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "NPM mahasiswa tidak ditemukan!";
    }
}

// Fetching data from data_mhs table
$query = "SELECT * FROM data_mhs";
$result = $db->query($query);

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
        
        <div class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-4">Kelola Mahasiswa</h1>
            <?php if (isset($successMsg)): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    <?= htmlspecialchars($successMsg); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($errorMsg)): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                    <?= htmlspecialchars($errorMsg); ?>
                </div>
            <?php endif; ?>
            <div class="bg-white shadow rounded p-4 mb-6">
                <h2 class="font-semibold text-lg mb-4">Daftar Mahasiswa</h2>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b">NPM</th>
                            <th class="py-2 px-4 border-b">Nama Mahasiswa</th>
                            <th class="py-2 px-4 border-b">Semester</th>
                            <th class="py-2 px-4 border-b">Username</th>
                            <th class="py-2 px-4 border-b">Password</th>
                            <th class="py-2 px-4 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['npm_mhs']); ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['nama_mhs']); ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['semester_mhs']); ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['username_mhs']); ?></td>
                                    <td class="py-2 px-4 border-b">********</td>
                                    <td class="py-2 px-4 border-b">
                                    <div class="flex items-center space-x-2">
                                        <!-- Tombol Edit -->
                                        <a href="editMahasiswa.php?npm_mhs=<?= htmlspecialchars($row['npm_mhs']); ?>" class="text-blue-500 hover:text-blue-700 py-1 px-3 rounded border border-blue-500">Edit</a>
                                        <!-- Tombol Hapus -->
                                        <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?');" class="inline">
                                            <input type="hidden" name="delete_id" value="<?= htmlspecialchars($row['npm_mhs']); ?>">
                                            <button type="submit" name="deleteMahasiswa" class="text-red-500 hover:text-red-700 py-1 px-3 rounded border border-red-500">Hapus</button>
                                        </form>
                                    </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-2 px-4 border-b text-center">Tidak ada mahasiswa ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div>
                <button onclick="window.location.href='tambahMahasiswa.php'" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
                    Tambah Mahasiswa
                </button>
            </div>
        </div>
    </div>
</body>
</html>
