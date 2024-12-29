<?php
include "../../service/database.php"; 
session_start();

// Ambil NPM Mahasiswa dari URL
$npm_mhs = $_GET['npm_mhs'] ?? null;

if (!$npm_mhs) {
    die("NPM Mahasiswa tidak ditemukan!");
}

// Ambil data mahasiswa berdasarkan NPM
$query = "SELECT * FROM data_mhs WHERE npm_mhs = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $npm_mhs);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Data mahasiswa tidak ditemukan!");
}

// Proses pengeditan
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editMahasiswa"])) {
    $nama_mhs = $_POST["nama_mhs"] ?? null;
    $semester_mhs = $_POST["semester_mhs"] ?? null;

    // Pastikan username tetap dengan nol di awal jika perlu
    $username_mhs = isset($_POST["username_mhs"]) ? str_pad($_POST["username_mhs"], 5, '0', STR_PAD_LEFT) : $row['username_mhs'];

    // Cek apakah password baru dimasukkan, jika ya enkripsi
    $password_mhs = isset($_POST["password_mhs"]) ? password_hash($_POST["password_mhs"], PASSWORD_BCRYPT) : $row['password_mhs'];

    if ($nama_mhs && $semester_mhs && $username_mhs && $password_mhs) {
        // Update query
        $query = "UPDATE data_mhs SET nama_mhs = ?, semester_mhs = ?, username_mhs = ?, password_mhs = ? WHERE npm_mhs = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssss", $nama_mhs, $semester_mhs, $username_mhs, $password_mhs, $npm_mhs);

        if ($stmt->execute()) {
            $successMsg = "Data mahasiswa berhasil diperbarui.";
            header("Location: dataMahasiswa.php"); // Redirect ke halaman daftar mahasiswa
            exit();
        } else {
            $errorMsg = "Gagal memperbarui data mahasiswa: " . $stmt->error;
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
    <title>Edit Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center mb-6">Edit Mahasiswa</h2>

            <!-- Menampilkan pesan error atau sukses -->
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

            <!-- Form Edit Mahasiswa -->
            <form method="POST">
                <div class="mb-4">
                    <label for="npm_mhs" class="block text-sm font-medium text-gray-700">NPM Mahasiswa</label>
                    <input type="text" id="npm_mhs" name="npm_mhs" value="<?= htmlspecialchars($row['npm_mhs']); ?>" class="w-full mt-1 p-2 border rounded-md" readonly>
                </div>

                <div class="mb-4">
                    <label for="nama_mhs" class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
                    <input type="text" id="nama_mhs" name="nama_mhs" value="<?= htmlspecialchars($row['nama_mhs']); ?>" class="w-full mt-1 p-2 border rounded-md" required>
                </div>

                <div class="mb-4">
                    <label for="semester_mhs" class="block text-sm font-medium text-gray-700">Semester</label>
                    <input type="number" id="semester_mhs" name="semester_mhs" value="<?= htmlspecialchars($row['semester_mhs']); ?>" class="w-full mt-1 p-2 border rounded-md" required min="1" max="8">
                </div>

                <div class="mb-4">
                    <label for="username_mhs" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username_mhs" name="username_mhs" value="<?= htmlspecialchars($row['username_mhs']); ?>" class="w-full mt-1 p-2 border rounded-md" required>
                </div>

                <div class="mb-4">
                    <label for="password_mhs" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password_mhs" name="password_mhs" class="w-full mt-1 p-2 border rounded-md">
                    <small class="text-gray-500">Kosongkan jika tidak ingin mengubah password.</small>
                </div>

                <div class="flex justify-between">
                    <button type="submit" name="editMahasiswa" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Update</button>
                    <a href="dataMahasiswa.php" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-500">Batal/Kembali</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
