<?php
include "../service/database.php";
session_start();

if (isset($_POST['login'])) {
    $npm = $_POST['npm'];
    $password = $_POST['password'];
    $status = strtolower($_POST['status']); // Selalu ubah status ke huruf kecil

    // Cek jika status tidak dipilih
    if (empty($status)) {
        echo "<script>alert('Silakan pilih jenis pengguna!');</script>";
        exit;
    }

    // Query berdasarkan status
    if ($status === "mahasiswa") {
        $sql = "SELECT * FROM data_mhs WHERE npm_mhs=?";
    } elseif ($status === "admin") {
        $sql = "SELECT * FROM user_admin WHERE nim=?";
    } else {
        echo "<script>alert('Jenis pengguna tidak valid!');</script>";
        exit;
    }

    // Eksekusi query dengan prepared statements
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $npm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Verifikasi password jika status mahasiswa
        if ($status === "mahasiswa") {
            // Verifikasi password yang terenkripsi
            if (password_verify($password, $data['password_mhs'])) {
                $_SESSION['npm_mhs'] = $data['npm_mhs'];
                header("Location: Mahasiswa/dashboardMahasiswa.php");
                exit;
            } else {
                echo "<script>alert('Username atau Password salah!');</script>";
            }
        } elseif ($status === "admin") {
            // Verifikasi password untuk admin (asumsikan password sudah terenkripsi)
                header("Location: Admin/dashboardAdmin.php");
                exit;
        } else {
                echo "<script>alert('Username atau Password salah!');</script>";
            }
    } else {
        echo "<script>alert('Username atau Password salah!');</script>";
    }

    $stmt->close();
}

$db->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Add Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- CSS Stylesheet -->
    <style>
        /* Reset Styles */
        body, html {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Background Styling */
        body {
            background: linear-gradient(135deg, #4dd0e1, #1976d2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Login Card Container */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Login Card Styling */
        .login-card {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 360px;
            border: 1px solid #e0e0e0;
        }

        /* Logo Styling */
        .logo img {
            width: 150px;
            margin-bottom: 20px;
        }

        /* Title Styling */
        h2 {
            font-size: 20px;
            color: #333333;
            margin-bottom: 25px;
            font-weight: bold;
        }

        /* Input Group Styling */
        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 12px 15px;
            padding-left: 40px;
            border: 2px solid #cccccc;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .input-group input:focus, .input-group select:focus {
            border-color: #1976d2;
            box-shadow: 0 0 5px rgba(25, 118, 210, 0.3);
        }

        .input-group label {
            font-size: 12px;
            color: #333333;
            margin-bottom: 5px;
            display: block;
            font-weight: bold;
        }

        .input-group .icon {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #888888;
            font-size: 16px;
        }

        /* Button Group Styling */
        .button-group {
            display: flex;
            justify-content: center; /* Menempatkan tombol di tengah */
            align-items: center;
            margin-top: 10px;
        }

        .login-button {
            background-color: #1976d2;
            color: #ffffff;
            border: none; /* Border lebih tebal */
            padding: 12px 0; /* Padding atas dan bawah, tanpa horizontal */
            width: 100%; /* Sesuaikan lebar dengan elemen input */
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            box-sizing: border-box; /* Pastikan padding tidak memengaruhi ukuran */
        }

        .login-button:hover {
            background-color: #145ca8;
            border-color: #1976d2; /* Perubahan warna border saat hover */
        }

        /* Responsive Styling */
        @media (max-width: 480px) {
            .login-card {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <img src="logo.png" alt="University Logo">
            </div>
            <h2>Sistem KRS Unkhair</h2>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <i class="fas fa-user icon"></i>
                    <input type="text" name="npm" placeholder="Username/NIM" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <label for="user-type">JENIS PENGGUNA</label>
                    <select id="user-type" name="status" required>
                        <option value="">- Pilih -</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="button-group">
                    <button type="submit" class="login-button" name="login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
