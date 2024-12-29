<?php 
include "../../service/database.php"; 
session_start();

// Ambil ID mata kuliah dari URL
$kode_mk = $_GET['kode'] ?? null;

// Variabel untuk pesan
$successMsg = '';
$errorMsg = '';

// Jika tidak ada kode mata kuliah, kembalikan ke halaman daftar
if (!$kode_mk) {
    die("Kode mata kuliah tidak ditemukan!");
}

// Ambil data mata kuliah berdasarkan kode
$query = "SELECT * FROM data_mk WHERE kode_mk = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $kode_mk);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Jika data tidak ditemukan
if (!$row) {
    die("Mata kuliah tidak ditemukan!");
}

// Proses update mata kuliah
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editMataKuliah"])) {
    // Ambil data dari form
    $kode_mk_baru = trim($_POST["kode_mk"]);
    $nama_mk = trim($_POST["nama_mk"]);
    $sks = intval($_POST["sks"]);
    $semester = intval($_POST["semester"]);
    $status = trim($_POST["status"]);

    // Validasi input
    $errors = [];
    if (empty($kode_mk_baru)) $errors[] = "Kode mata kuliah tidak boleh kosong.";
    if (!preg_match('/^[A-Za-z0-9]+$/', $kode_mk_baru)) $errors[] = "Kode mata kuliah hanya boleh berisi huruf dan angka.";
    if (empty($nama_mk)) $errors[] = "Nama mata kuliah tidak boleh kosong.";
    if ($sks < 1 || $sks > 5) $errors[] = "Jumlah SKS harus antara 1 dan 5.";
    if ($semester < 1 || $semester > 8) $errors[] = "Semester harus antara 1 dan 8.";
    if (empty($status)) $errors[] = "Status mata kuliah harus dipilih.";

    if (empty($errors)) {
        // Query update
        $query = "UPDATE data_mk SET 
                    kode_mk = ?, 
                    nama_mk = ?, 
                    sks = ?, 
                    semester = ?, 
                    status = ?
                  WHERE kode_mk = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssisss", 
            $kode_mk_baru, 
            $nama_mk, 
            $sks, 
            $semester, 
            $status, 
            $kode_mk
        );

        if ($stmt->execute()) {
            $successMsg = "Mata kuliah berhasil diperbarui.";
            // Update $row dengan data terbaru untuk refresh form
            $row = [
                'kode_mk' => $kode_mk_baru,
                'nama_mk' => $nama_mk,
                'sks' => $sks,
                'semester' => $semester,
                'status' => $status,
            ];
        } else {
            $errorMsg = "Gagal memperbarui mata kuliah: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = implode(", ", $errors);
    }
}

// Tutup koneksi
$db->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mata Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Edit Mata Kuliah</h1>

    <?php if (!empty($successMsg)): ?>
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            <?= $successMsg; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <?= $errorMsg; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label for="kode_mk" class="block text-sm font-medium text-gray-700">Kode Mata Kuliah</label>
            <input type="text" id="kode_mk" name="kode_mk" 
                   value="<?= htmlspecialchars($row['kode_mk'] ?? ''); ?>" 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="nama_mk" class="block text-sm font-medium text-gray-700">Nama Mata Kuliah</label>
            <input type="text" id="nama_mk" name="nama_mk" 
                   value="<?= htmlspecialchars($row['nama_mk'] ?? ''); ?>" 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="sks" class="block text-sm font-medium text-gray-700">SKS</label>
            <input type="number" id="sks" name="sks" 
                   value="<?= htmlspecialchars($row['sks'] ?? ''); ?>" 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md" 
                   required min="1" max="4">
        </div>

        <div class="mb-4">
            <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
            <input type="number" id="semester" name="semester" 
                   value="<?= htmlspecialchars($row['semester'] ?? ''); ?>" 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md" 
                   required min="1" max="8">
        </div>

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status Mata Kuliah</label>
            <select id="status" name="status" 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md" required>
                <option value="WAJIB" <?= ($row['status'] ?? '') === 'WAJIB' ? 'selected' : ''; ?>>WAJIB</option>
                <option value="Pilihan" <?= ($row['status'] ?? '') === 'Pilihan' ? 'selected' : ''; ?>>Pilihan</option>
            </select>
        </div>

        <div class="flex justify-between">
            <button type="submit" name="editMataKuliah" 
                    class="bg-blue-500 text-white py-2 px-4 rounded">Update</button>
            <a href="mataKuliah.php" 
               class="bg-gray-300 text-gray-800 py-2 px-4 rounded">Batal/Kembali</a>
        </div>
    </form>
</div>
</body>
</html>