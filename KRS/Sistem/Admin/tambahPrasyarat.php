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

// Proses penambahan prasyarat mata kuliah
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addPrasyarat"])) {
    $kode_mk = $_POST["kode_mk"];
    $nilai_minimal = $_POST["nilai_minimal"];
    $kode_mk_prasyarat = $_POST["kode_mk_prasyarat"];

    if ($kode_mk && $nilai_minimal && $kode_mk_prasyarat) {
        $query = "INSERT INTO mk_prasyarat (kode_mk, nilai_minimal, kode_mk_prasyarat) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sss", $kode_mk, $nilai_minimal, $kode_mk_prasyarat);

        if ($stmt->execute()) {
            $successMsg = "Prasyarat mata kuliah berhasil ditambahkan.";
        } else {
            $errorMsg = "Gagal menambahkan prasyarat mata kuliah: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Harap lengkapi semua field yang wajib diisi!";
    }
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Prasyarat Mata Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-4">Tambah Prasyarat Mata Kuliah</h1>
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
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="kode_mk" class="block text-sm font-medium text-gray-700">Kode Mata Kuliah</label>
                    <input type="text" id="kode_mk" name="kode_mk" class="mt-1 block w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="nilai_minimal" class="block text-sm font-medium text-gray-700">Nilai Minimal</label>
                    <input type="text" id="nilai_minimal" name="nilai_minimal" class="mt-1 block w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="kode_mk_prasyarat" class="block text-sm font-medium text-gray-700">Kode Prasyarat</label>
                    <input type="text" id="kode_mk_prasyarat" name="kode_mk_prasyarat" class="mt-1 block w-full p-2 border rounded" required>
                </div>
                <div class="flex justify-end">
                    <a href="penentuanMatakuliah.php" class="py-2 px-4 bg-gray-500 text-white rounded mr-2">Batal</a>
                    <button type="submit" name="addPrasyarat" class="py-2 px-4 bg-blue-500 text-white rounded">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
