<?php
include "../../service/database.php";
session_start();

// Logout handler
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// var_dump($_POST);
// die();

// Periksa apakah data yang diperlukan ada dalam $_POST
if (isset($_POST['npm'], $_POST['kode-mk'], $_POST['nilai-mk'], $_POST['semester'])) {
    // Menyiapkan query dengan placeholder
    $stmt = $db->prepare("INSERT INTO data_khs (npm_mahasiswa, kode_mk, nilai, semester) 
                          VALUES (?, ?, ?, ?)");

    // Mengikat parameter untuk prepared statement
    // 's' untuk string, bisa disesuaikan dengan tipe data yang sesuai
    $stmt->bind_param('ssss', $_POST['npm'], $_POST['kode-mk'], $_POST['nilai-mk'], $_POST['semester']);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Redirect setelah berhasil
        header('Location: pengajuanKHS.php?semester_khs=' . $_POST['semester'] . '&npm_mhs=' . $_POST['npm']);
        exit();
    } else {
        // Tampilkan error jika eksekusi gagal
        echo "Error: " . $stmt->error;
        exit();
    }
} else {
    // Tindakan jika data tidak lengkap
    echo "Data tidak lengkap.";
    exit();
}
