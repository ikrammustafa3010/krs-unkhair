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

// Periksa apakah data yang diperlukan ada dalam $_POST
if (isset($_POST['hapus'])) {
    // Menyiapkan query DELETE dengan placeholder
    $stmt = $db->prepare("DELETE FROM data_khs WHERE id_khs = ?");

    // Mengikat parameter untuk prepared statement
    $stmt->bind_param('s', $_POST['hapus']);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Redirect ke halaman sebelumnya (HTTP_REFERER)
        header('Location: ' . $_SERVER['HTTP_REFERER']);
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
