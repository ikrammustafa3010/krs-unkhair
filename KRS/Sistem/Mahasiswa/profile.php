<?php
session_start();
include "../../service/database.php";

// Logout handler
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['npm_mhs'])) {
    header("Location: ../login.php");
    exit();
}

$npm = $_SESSION['npm_mhs'];

// Fetch user data based on npm
$query = "SELECT * FROM data_mhs WHERE npm_mhs = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $npm);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $namaLengkap = $_POST['namaLengkap'];
    $email = $_POST['email'];
    $noHp = $_POST['noHp'];
    $alamat = $_POST['alamat'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Encrypt the password if it's updated
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $hashedPassword = $user['password_mhs']; // Keep the current password if not updated
    }

    // Update query
    $updateQuery = "UPDATE data_mhs SET nama_mhs = ?, email_mhs = ?, no_tlpn = ?, alamat_mhs = ?, username_mhs = ?, password_mhs = ? WHERE npm_mhs = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bind_param("sssssss", $namaLengkap, $email, $noHp, $alamat, $username, $hashedPassword, $npm);
    if ($updateStmt->execute()) {
        // Refresh data after update
        header("Location: dashboardMahasiswa.php?update=success");
        exit();
    } else {
        $error = "Gagal mengupdate data!";
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
        <main class="flex-1 p-6">
            <section id="profil" class="container mx-auto px-6 py-8">
                <h2 class="text-2xl font-bold mb-4">Profil Mahasiswa</h2>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label for="namaLengkap" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                            <input type="text" id="namaLengkap" name="namaLengkap" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($user['nama_mhs']); ?>">
                        </div>
                        <div class="mb-4">
                            <label for="npm" class="block text-gray-700 font-medium mb-2">NPM</label>
                            <input type="text" id="npm" name="npm" class="w-full px-4 py-2 border rounded-lg bg-gray-100" value="<?php echo htmlspecialchars($user['npm_mhs']); ?>" disabled>
                        </div>
                        <div class="mb-4">
                            <label for="prodi" class="block text-gray-700 font-medium mb-2">Program Studi</label>
                            <input type="text" id="prodi" name="prodi" class="w-full px-4 py-2 border rounded-lg bg-gray-100" value="<?php echo htmlspecialchars($user['prodi_mhs']); ?>" disabled>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($user['email_mhs']); ?>">
                        </div>
                        <div class="mb-4">
                            <label for="noHp" class="block text-gray-700 font-medium mb-2">Nomor HP</label>
                            <input type="tel" id="noHp" name="noHp" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($user['no_tlpn']); ?>">
                        </div>
                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700 font-medium mb-2">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($user['alamat_mhs']); ?>">
                        </div>
                        <div class="mb-4">
                            <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                            <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($user['username_mhs']); ?>">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg" placeholder="Kosongkan jika tidak ingin mengganti">
                        </div>
                        <button type="submit" name="update" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
