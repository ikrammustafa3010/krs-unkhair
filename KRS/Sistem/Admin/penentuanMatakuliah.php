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

// Mengambil data dari tabel mk_prasyarat
$query = "SELECT * FROM mk_prasyarat";
$result = $db->query($query);

// Proses hapus prasyarat mata kuliah
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deletePrasyarat"])) {
    $delete_kode_mk = $_POST["delete_kode_mk"];
    $delete_kode_mk_prasyarat = $_POST["delete_kode_mk_prasyarat"];

    if ($delete_kode_mk && $delete_kode_mk_prasyarat) {
        $query = "DELETE FROM mk_prasyarat WHERE kode_mk = ? AND kode_mk_prasyarat = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $delete_kode_mk, $delete_kode_mk_prasyarat);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Prasyarat mata kuliah berhasil dihapus.";
            header("Location: penentuanMatakuliah.php"); // Redirect ke halaman daftar mata kuliah
            exit();
        } else {
            $errorMsg = "Gagal menghapus prasyarat mata kuliah: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Data prasyarat mata kuliah tidak ditemukan!";
    }
}

// Proses edit prasyarat mata kuliah
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editPrasyarat"])) {
    $edit_kode_mk = $_POST["edit_kode_mk"];
    $edit_nilai_minimal = $_POST["edit_nilai_minimal"];
    $edit_kode_mk_prasyarat = $_POST["edit_kode_mk_prasyarat"];

    if ($edit_kode_mk && $edit_nilai_minimal && $edit_kode_mk_prasyarat) {
        $query = "UPDATE mk_prasyarat SET nilai_minimal = ?, kode_mk_prasyarat = ? WHERE kode_mk = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sss", $edit_nilai_minimal, $edit_kode_mk_prasyarat, $edit_kode_mk);

        if ($stmt->execute()) {
            $successMsg = "Prasyarat mata kuliah berhasil diperbarui.";
        } else {
            $errorMsg = "Gagal memperbarui prasyarat mata kuliah: " . $stmt->error;
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
    <title>Sistem Manajemen KRS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex">
        <div class="bg-blue-600 w-64 min-h-screen text-white p-4">
            <!-- Sidebar -->
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
                <a href="penentuanMatakuliah.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-cogs mr-2"></i> Prasyarat Mata Kuliah
                </a>
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
                <h1 class="text-2xl font-bold mb-4">Kelola Prasyarat Mata Kuliah</h1>
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

                <!-- Tabel Prasyarat Mata Kuliah -->
                <div class="bg-white shadow rounded p-4 mb-6">
                    <h2 class="font-semibold text-lg mb-4">Daftar Prasyarat Mata Kuliah</h2>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 border-b">Kode Mata Kuliah</th>
                                <th class="py-2 px-4 border-b">Nilai Minimal</th>
                                <th class="py-2 px-4 border-b">Kode Prasyarat</th>
                                <th class="py-2 px-4 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['kode_mk']); ?></td>
                                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['nilai_minimal']); ?></td>
                                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['kode_mk_prasyarat']); ?></td>
                                        <td class="py-2 px-4 border-b flex items-center">
                                            <!-- Tombol Edit -->
                                            <button onclick="openEditModal('<?= htmlspecialchars($row['kode_mk']); ?>', '<?= htmlspecialchars($row['nilai_minimal']); ?>', '<?= htmlspecialchars($row['kode_mk_prasyarat']); ?>')" class="text-blue-500 hover:text-blue-700 py-1 px-3 rounded border border-blue-500">Edit</button>
                                            <!-- Tombol Hapus -->
                                            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus prasyarat ini?');" class="inline-block ml-2">
                                                <input type="hidden" name="delete_kode_mk" value="<?= htmlspecialchars($row['kode_mk']); ?>">
                                                <input type="hidden" name="delete_kode_mk_prasyarat" value="<?= htmlspecialchars($row['kode_mk_prasyarat']); ?>">
                                                <button type="submit" name="deletePrasyarat" class="text-red-500 hover:text-red-700 py-1 px-3 rounded border border-red-500">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-2 px-4 border-b text-center">Tidak ada data prasyarat yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- Tombol Tambah Prasyarat -->
            <div>
                <button onclick="window.location.href='tambahPrasyarat.php'" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
                    Tambah Prasyarat
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Prasyarat -->
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Edit Prasyarat Mata Kuliah</h2>
            <form method="POST" action="">
                <input type="hidden" id="edit_kode_mk" name="edit_kode_mk">
                <div class="mb-4">
                    <label for="edit_nilai_minimal" class="block text-sm font-medium text-gray-700">Nilai Minimal</label>
                    <input type="text" id="edit_nilai_minimal" name="edit_nilai_minimal" class="mt-1 block w-full p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="edit_kode_mk_prasyarat" class="block text-sm font-medium text-gray-700">Kode Prasyarat</label>
                    <input type="text" id="edit_kode_mk_prasyarat" name="edit_kode_mk_prasyarat" class="mt-1 block w-full p-2 border rounded">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()" class="py-2 px-4 bg-gray-500 text-white rounded mr-2">Batal</button>
                    <button type="submit" name="editPrasyarat" class="py-2 px-4 bg-blue-500 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(kodeMk, nilaiMinimal, kodeMkPrasyarat) {
            document.getElementById('edit_kode_mk').value = kodeMk;
            document.getElementById('edit_nilai_minimal').value = nilaiMinimal;
            document.getElementById('edit_kode_mk_prasyarat').value = kodeMkPrasyarat;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>
