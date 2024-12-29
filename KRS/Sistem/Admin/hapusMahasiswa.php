<?php
include "../../service/database.php"; // Make sure the path is correct
session_start();

// Logout handler
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php"); // Adjust the path based on your structure
    exit();
}

// Proses Hapus Mahasiswa
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteMahasiswa"])) {
    $delete_id = $_POST["delete_id"];

    if ($delete_id) {
        $query = "DELETE FROM data_mhs WHERE npm_mhs = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $delete_id);

        if ($stmt->execute()) {
            $successMsg = "Mahasiswa berhasil dihapus.";
        } else {
            $errorMsg = "Gagal menghapus mahasiswa: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "NPM mahasiswa tidak ditemukan!";
    }
}

$db->close();

?>