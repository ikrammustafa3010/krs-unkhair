<?php
include "../../service/database.php";
session_start();

// Logout handler
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("location: ../login.php");
    exit();
}

// Proses penambahan mata kuliah
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addMataKuliah"])) {
    $kode_mk = $_POST["kode_mk"];
    $nama_mk = $_POST["nama_mk"];
    $sks_mk = $_POST["sks"];
    $semester_mk = $_POST["semester"];
    $status_mk = $_POST["status"];

    if ($kode_mk && $nama_mk && $sks && $semester && $status) {
        $query = "INSERT INTO data_mk (kode_mk, nama_mk, sks, semester, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssissss", $kode_mk, $nama_mk, $sks, $semester, $status);

        if ($stmt->execute()) {
            $successMsg = "Mata kuliah berhasil ditambahkan.";
        } else {
            $errorMsg = "Gagal menambahkan mata kuliah: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Harap lengkapi semua field yang wajib diisi!";
    }
}

// Mengambil data dari tabel data_mk
$query = "SELECT * FROM data_mk";
$result = $db->query($query);

// Proses Hapus Mata Kuliah
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteMataKuliah"])) {
    $delete_kode = $_POST["delete_kode"];

    if ($delete_kode) {
        $query = "DELETE FROM data_mk WHERE kode_mk = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $delete_kode);  // Gunakan "s" untuk string

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Mata kuliah berhasil dihapus.";
            header("Location: mataKuliah.php");  // Redirect ke halaman daftar mata kuliah
            exit();
        } else {
            $errorMsg = "Gagal menghapus mata kuliah: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Kode mata kuliah tidak ditemukan!";
    }
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
            <section id="beranda" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h1 class="text-2xl font-bold mb-4">Kelola Mata Kuliah</h1>
            <?php if (isset($successMsg)): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    <?= $successMsg; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($errorMsg)): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                    <?= $errorMsg; ?>
                </div>
            <?php endif; ?>

            <!-- Tabel Mata Kuliah -->
            <div class="bg-white shadow rounded p-4 mb-6">
                <h2 class="font-semibold text-lg mb-4">Daftar Mata Kuliah</h2>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b">Kode</th>
                            <th class="py-2 px-4 border-b">Nama Mata Kuliah</th>
                            <th class="py-2 px-4 border-b">SKS</th>
                            <th class="py-2 px-4 border-b">Semester</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['kode_mk']); ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['nama_mk']); ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['sks']); ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['semester']); ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['status']); ?></td>
                                    <td class="py-2 px-4 border-b">
                                        <!-- Tombol Edit -->
                                        <div class="flex items-center space-x-2">
                                            <form method="GET" action="editMataKuliah.php" class="inline">
                                                <input type="hidden" name="kode" value="<?= $row['kode_mk']; ?>">
                                                <button type="submit" class="text-blue-500 hover:text-blue-700 py-1 px-3 rounded border border-blue-500">Edit</button>
                                            </form>
                                        <!-- Tombol Hapus -->
                                            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?');" class="inline">
                                                <input type="hidden" name="delete_kode" value="<?= htmlspecialchars($row['kode_mk']); ?>">
                                                <button type="submit" name="deleteMataKuliah" class="text-red-500 hover:text-red-700 py-1 px-3 rounded border border-red-500">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-2 px-4 border-b text-center">Tidak ada mata kuliah yang ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            </section>
            <!-- Tombol Tambah Mata Kuliah -->
            <div>
            <button onclick="window.location.href='tambahMataKuliah.php'" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
                Tambah Mata Kuliah
            </button>
            </div>
        </div>
    </div>
</body>
</html>
