<?php
session_start();
include '../config/controller.php';

// Pastikan user telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Pastikan ada ID buku yang dikirimkan
if (isset($_GET['id'])) {
    $id_buku = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Periksa apakah buku tersedia
    $cek_buku = "SELECT status FROM buku WHERE id_buku = '$id_buku'";
    $result = mysqli_query($conn, $cek_buku);
    $buku = mysqli_fetch_assoc($result);

    if ($buku['status'] === 'Tersedia') {
        // Ubah status buku menjadi "Dipinjam"
        $update_status = "UPDATE buku SET ketersediaan = 'Dipinjam' WHERE id_buku = '$id_buku'";
        mysqli_query($conn, $update_status);

        // Tambahkan ke tabel peminjaman
        $tgl_pinjam = date("Y-m-d");
        $tgl_kembali = date("Y-m-d", strtotime("+7 days")); // Misal batas peminjaman 7 hari
        $insert_pinjam = "INSERT INTO peminjaman (user_id, id_buku, tgl_pinjam, tgl_kembali, ketersediaan) 
                          VALUES ('$user_id', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'Dipinjam')";
        mysqli_query($conn, $insert_pinjam);

        echo "<script>alert('Buku berhasil dipinjam!'); window.location.href='index_pengunjung.php';</script>";
    } else {
        echo "<script>alert('Buku sedang dipinjam oleh pengguna lain!'); window.location.href='index_pengunjung.php';</script>";
    }
} else {
    echo "<script>alert('Buku tidak ditemukan!'); window.location.href='index_pengunjung.php';</script>";
}

mysqli_close($conn);
?>
