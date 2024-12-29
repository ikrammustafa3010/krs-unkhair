<?php
    include "../../service/database.php"; // Pastikan path file sudah benar
    session_start();

    // Proses penambahan mahasiswa
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addMahasiswa"])) {
        // Menerima input dari form
        $npm_mhs = trim($_POST["npm_mhs"]);
        $nama_mhs = trim($_POST["nama_mhs"]);
        $semester_mhs_input = trim($_POST["semester_mhs"]); // Mendapatkan nilai semester
        $username_mhs = trim($_POST["username_mhs"]);
        $password_mhs = trim($_POST["password_mhs"]); // Password asli
        $email_mhs = trim($_POST["email_mhs"]); // Email
        $no_tlpn = trim($_POST["no_tlpn"]); // No Telepon
        $alamat_mhs = trim($_POST["alamat_mhs"]); // Alamat
        $prodi_mhs = "Informatika"; // Anda bisa menyesuaikan ini jika perlu

        // Hash password
        $password_hashed = password_hash($password_mhs, PASSWORD_BCRYPT);

        // Pemetaan semester dari string ke integer
        $semester_map = [
            'Semester 1' => 1,
            'Semester 2' => 2,
            'Semester 3' => 3,
            'Semester 4' => 4,
            'Semester 5' => 5,
            'Semester 6' => 6,
            'Semester 7' => 7,
            'Semester 8' => 8
        ];

        // Mengambil nilai semester yang sesuai
        $semester_mhs = isset($semester_map[$semester_mhs_input]) ? $semester_map[$semester_mhs_input] : null;

        // Memastikan semua field yang diperlukan diisi
        if (!empty($npm_mhs) && !empty($nama_mhs) && !empty($semester_mhs) && !empty($username_mhs) && !empty($password_mhs)) {
            $query = "INSERT INTO data_mhs 
                (npm_mhs, nama_mhs, semester_mhs, username_mhs, password_mhs, prodi_mhs, email_mhs, no_tlpn, alamat_mhs, create_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE)";
            
            $stmt = $db->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ssissssss", $npm_mhs, $nama_mhs, $semester_mhs, $username_mhs, $password_hashed, $prodi_mhs, $email_mhs, $no_tlpn, $alamat_mhs);
                
                // Debugging: Periksa apakah statement berhasil dieksekusi
                if ($stmt->execute()) {
                    $successMsg = "Mahasiswa berhasil ditambahkan.";
                } else {
                    // Tambahkan lebih banyak detail untuk debugging
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

    $db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center mb-6">Tambah Data Mahasiswa</h2>

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

            <!-- Form Tambah Mahasiswa -->
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="npm_mhs" class="block text-sm font-medium text-gray-700">NPM</label>
                    <input type="text" id="npm_mhs" name="npm_mhs" class="w-full mt-1 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="nama_mhs" class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
                    <input type="text" id="nama_mhs" name="nama_mhs" class="w-full mt-1 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="semester_mhs" class="block text-sm font-medium text-gray-700">Semester</label>
                    <select id="semester_mhs" name="semester_mhs" class="w-full mt-1 p-2 border rounded" required>
                        <option value="">-- Pilih Semester --</option>
                        <option value="Semester 1">Semester 1</option>
                        <option value="Semester 2">Semester 2</option>
                        <option value="Semester 3">Semester 3</option>
                        <option value="Semester 4">Semester 4</option>
                        <option value="Semester 5">Semester 5</option>
                        <option value="Semester 6">Semester 6</option>
                        <option value="Semester 7">Semester 7</option>
                        <option value="Semester 8">Semester 8</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="username_mhs" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username_mhs" name="username_mhs" class="w-full mt-1 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="password_mhs" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password_mhs" name="password_mhs" class="w-full mt-1 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="email_mhs" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email_mhs" name="email_mhs" class="w-full mt-1 p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="no_tlpn" class="block text-sm font-medium text-gray-700">No Telepon</label>
                    <input type="text" id="no_tlpn" name="no_tlpn" class="w-full mt-1 p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="alamat_mhs" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="alamat_mhs" name="alamat_mhs" class="w-full mt-1 p-2 border rounded"></textarea>
                </div>
                <div class="flex justify-between">
                    <button type="submit" name="addMahasiswa" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Tambah Mahasiswa</button>
                    <button type="button" onclick="window.location.href='dataMahasiswa.php'" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-500">Kembali</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
