<?php
include "koneksi.php";

// Tempat utk menaruh function aplikasi
function add_buku($post)
{
    global $db;
    $judul = $post['judul'] ?? '';
    $penulis = $post['penulis'] ?? '';
    $penerbit = $post['penerbit'] ?? '';
    $tahun_terbit = isset($post['tahun_terbit']) && is_numeric($post['tahun_terbit']) ? (int)$post['tahun_terbit'] : null;

    if ($tahun_terbit === null) {
        throw new InvalidArgumentException("tahun_terbit is required and must be an integer.");
    }

    $query = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit) VALUES ('$judul', '$penulis', '$penerbit', '$tahun_terbit')";
    if (mysqli_query($db, $query)) {
        return mysqli_affected_rows($db);
    } else {
        throw new mysqli_sql_exception("Error: " . $query . "<br>" . mysqli_error($db));
    }
}

function delete_buku($id)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM buku WHERE id_buku = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}

function edit_buku($post)
{
    global $db;
    // Perbaikan: Mengambil id_buku dari $post, bukan dari $_GET
    $id = $post['id_buku'];
    $judul = strip_tags($post['judul']);
    $penulis = strip_tags($post['penulis']);
    $penerbit = strip_tags($post['penerbit']);
    $tahun_terbit = strip_tags($post['tahun_terbit']);

    // Perbaikan: Menambahkan tipe data pada bind_param
    $stmt = $db->prepare("UPDATE buku SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ? WHERE id_buku = ?");
    $stmt->bind_param("sssii", $judul, $penulis, $penerbit, $tahun_terbit, $id);
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}

function get_buku_by_id($id) {
    $conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM buku WHERE id_buku = $id";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row;
    } else {
        return false;
    }

    mysqli_close($conn);
}

function add_member($post)
{
    global $db;
    $nama = $post['nama_anggota'] ?? '';
    $alamat = $post['alamat'] ?? '';
    $email = $post['email'] ?? '';
    $tgl_gabung = isset($post['tgl_gabung']) && is_numeric($post['tgl_gabung']) ? date('Y-m-d', $post['tgl_gabung']) : 'NULL';

    $query = "INSERT INTO anggota (nama_anggota, alamat, email, tgl_gabung) VALUES ('$nama', '$alamat', '$email', $tgl_gabung)";
    if (mysqli_query($db, $query)) {
        return mysqli_affected_rows($db);
    } else {
        throw new mysqli_sql_exception("Error: " . $query . "<br>" . mysqli_error($db));
    }
}

function delete_member($id)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}

function pinjamBuku($id_anggota, $id_buku) {
    global $conn;
    
    // Check if member exists
    $query = "SELECT * FROM anggota WHERE id_anggota = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_anggota);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return [
            'status' => false,
            'message' => 'ID Anggota tidak ditemukan.'
        ];
    }
    
    // Check if book exists and is available
    $query = "SELECT * FROM buku WHERE id_buku = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_buku);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return [
            'status' => false,
            'message' => 'ID Buku tidak ditemukan.'
        ];
    }
    
    // Check if book is already borrowed
    $query = "SELECT * FROM peminjaman WHERE id_buku = ? AND status = 'dipinjam'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_buku);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return [
            'status' => false,
            'message' => 'Buku sedang dipinjam oleh anggota lain.'
        ];
    }
    
    // Get current date for tgl_pinjam
    $tgl_pinjam = date('Y-m-d');
    
    // Default return date is 14 days from now
    $tgl_kembali = date('Y-m-d', strtotime('+14 days'));
    
    // Insert new lending record
    $query = "INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, tgl_kembali, status) VALUES (?, ?, ?, ?, 'dipinjam')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiss", $id_anggota, $id_buku, $tgl_pinjam, $tgl_kembali);
    
    if ($stmt->execute()) {
        $id_peminjaman = $conn->insert_id;
        return [
            'status' => true,
            'message' => 'Peminjaman berhasil.',
            'data' => [
                'id_peminjaman' => $id_peminjaman,
                'id_anggota' => $id_anggota,
                'id_buku' => $id_buku,
                'tgl_pinjam' => $tgl_pinjam,
                'tgl_kembali' => $tgl_kembali,
                'status' => 'dipinjam'
            ]
        ];
    } else {
        return [
            'status' => false,
            'message' => 'Gagal melakukan peminjaman: ' . $conn->error
        ];
    }
}

function kembalikanBuku($id_peminjaman) {
    global $conn;
    
    // Check if lending record exists
    $query = "SELECT * FROM peminjaman WHERE id_peminjaman = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_peminjaman);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return [
            'status' => false,
            'message' => 'ID Peminjaman tidak ditemukan.'
        ];
    }
    
    $peminjaman = $result->fetch_assoc();
    
    if ($peminjaman['status'] != 'dipinjam') {
        return [
            'status' => false,
            'message' => 'Buku ini tidak dalam status dipinjam.'
        ];
    }
    
    // Update lending status to 'kembali'
    $query = "UPDATE peminjaman SET status = 'kembali', tgl_kembali = ? WHERE id_peminjaman = ?";
    $tgl_kembali_actual = date('Y-m-d');
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $tgl_kembali_actual, $id_peminjaman);
    
    if ($stmt->execute()) {
        return [
            'status' => true,
            'message' => 'Pengembalian buku berhasil.',
            'data' => [
                'id_peminjaman' => $id_peminjaman,
                'tgl_kembali_actual' => $tgl_kembali_actual
            ]
        ];
    } else {
        return [
            'status' => false,
            'message' => 'Gagal melakukan pengembalian: ' . $conn->error
        ];
    }
}