<?php
include "../../service/database.php";
session_start();

if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("location: ../login.php");
    exit();
}

$successMsg = '';
$errorMsg = '';

// Fungsi untuk mengecek apakah kode_mk sudah ada
function kodeSudahAda($db, $kode_mk) {
    $query = "SELECT 1 FROM data_mk WHERE kode_mk = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $kode_mk);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addMataKuliah"])) {
    $kode_mk = trim($_POST["kode_mk"]);
    $nama_mk = trim($_POST["nama_mk"]);
    $sks = intval($_POST["sks_mk"]);
    $semester = intval($_POST["semester_mk"]);
    $status = trim($_POST["status_mk"]);

    // Validasi input
    if (!preg_match('/^[A-Za-z0-9]+$/', $kode_mk)) {
        $errorMsg = "Kode mata kuliah hanya boleh mengandung huruf dan angka.";
    } elseif (kodeSudahAda($db, $kode_mk)) {
        $errorMsg = "Kode mata kuliah sudah terdaftar.";
    } elseif ($sks < 1 || $sks > 5) {
        $errorMsg = "Jumlah SKS harus antara 1 dan 5.";
    } elseif (empty($semester) || empty($status)) {
        $errorMsg = "Semester dan status mata kuliah harus dipilih.";
    } else {
        // Query untuk menambahkan data mata kuliah
        $query = "INSERT INTO data_mk (kode_mk, nama_mk, sks, semester, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssiss", $kode_mk, $nama_mk, $sks, $semester, $status);

        if ($stmt->execute()) {
            $successMsg = "Mata kuliah berhasil ditambahkan.";
            $_POST = []; // Reset input form
        } else {
            $errorMsg = "Terjadi kesalahan saat menambahkan mata kuliah: " . $stmt->error;
        }
        $stmt->close();
    }
}

$db->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center mb-6">Tambah Mata Kuliah</h2>

            <!-- Menampilkan pesan error atau sukses -->
            <?php if (!empty($successMsg)): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    <?= htmlspecialchars($successMsg); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($errorMsg)): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                    <?= htmlspecialchars($errorMsg); ?>
                </div>
            <?php endif; ?>

            <!-- Form Tambah Mata Kuliah -->
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="kode_mk" class="block text-sm font-medium text-gray-700">Kode Mata Kuliah</label>
                    <input type="text" id="kode_mk" name="kode_mk" class="w-full mt-1 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="nama_mk" class="block text-sm font-medium text-gray-700">Nama Mata Kuliah</label>
                    <input type="text" id="nama_mk" name="nama_mk" class="w-full mt-1 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="sks_mk" class="block text-sm font-medium text-gray-700">Jumlah SKS</label>
                    <input type="number" id="sks_mk" name="sks_mk" class="w-full mt-1 p-2 border rounded" required min="1" max="4">
                </div>
                <div class="mb-4">
                    <label for="semester_mk" class="block text-sm font-medium text-gray-700">Semester</label>
                    <select id="semester_mk" name="semester_mk" class="w-full mt-1 p-2 border rounded" required>
                        <option value="">-- Pilih Semester --</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                        <option value="3">Semester 3</option>
                        <option value="4">Semester 4</option>
                        <option value="5">Semester 5</option>
                        <option value="6">Semester 6</option>
                        <option value="7">Semester 7</option>
                        <option value="8">Semester 8</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="status_mk" class="block text-sm font-medium text-gray-700">Pilih Status Mata Kuliah</label>
                    <select id="status_mk" name="status_mk" class="w-full mt-1 p-2 border rounded" required>
                        <option value="" selected disabled>Pilih Status</option>
                        <option value="WAJIB">WAJIB</option>
                        <option value="Pilihan">Pilihan</option>
                    </select>
                </div>
                <div class="flex justify-between">
                    <button type="submit" name="addMataKuliah" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Tambah</button>
                    <button type="button" onclick="window.location.href='mataKuliah.php'" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-500">Kembali</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
