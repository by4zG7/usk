<?php
// Koneksi ke database
include "koneksi.php";

// Fungsi untuk menambahkan data buku ke database
function add_buku($post)
{
    global $db;
    // Validasi data input
    $judul = $post['judul'] ?? '';
    $penulis = $post['penulis'] ?? '';
    $penerbit = $post['penerbit'] ?? '';
    $tahun_terbit = isset($post['tahun_terbit']) && is_numeric($post['tahun_terbit']) ? (int)$post['tahun_terbit'] : null;
    
    // Jika tahun terbit tidak valid, maka akan muncul pesan kesalahan
    if ($tahun_terbit === null) {
        throw new InvalidArgumentException("tahun_terbit harus berupa angka.");
    }
    
    // Query SQL untuk menambahkan data buku ke database dan eksekusinya
    $query = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit) VALUES ('$judul', '$penulis', '$penerbit', '$tahun_terbit')";
    if (mysqli_query($db, $query)) {
        return mysqli_affected_rows($db);
    } else {
        throw new mysqli_sql_exception("Error: " . $query . "<br>" . mysqli_error($db)); // Pesan error jika query gagal
    }
}

// Fungsi untuk menghapus data buku dari database
function delete_buku($id)
{
    // Koneksi ke database
    global $db;
    
    // Query untuk menghapus data buku dari database
    $stmt = $db->prepare("DELETE FROM buku WHERE id_buku = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}

// Fungsi untuk mengambil data buku berdasarkan ID
function get_buku_by_id($id) {
    // Koneksi ke database
    $conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
    
    // Query untuk mengambil data buku berdasarkan ID
    $query = "SELECT * FROM buku WHERE id_buku = " . $id;
    
    // Eksekusi query
    $result = mysqli_query($conn, $query);
    
    // Check if query was successful and returned a row
    if ($result && mysqli_num_rows($result) > 0) {
        $buku = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        mysqli_close($conn);
        return $buku;
    } else {
        // Kembalikan pesan error jika query gagal
        echo "<!-- Debug: Query error or no rows returned. Query: $query | Error: " . mysqli_error($conn) . " -->";
        mysqli_close($conn);
        return false;
    }
}

// Fungsi untuk mengedit data buku di database
function edit_buku($data) {
    // Koneksi ke database
    $conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
    
    // Sanitize and validate input data
    $id_buku = isset($data['id_buku']) ? (int)$data['id_buku'] : 0;
    $judul = mysqli_real_escape_string($conn, $data['judul']);
    $penulis = mysqli_real_escape_string($conn, $data['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $data['penerbit']);
    $tahun_terbit = mysqli_real_escape_string($conn, $data['tahun_terbit']);
    
    // Query untuk mengedit data buku di database
    $query = "UPDATE buku SET 
              judul = '$judul', 
              penulis = '$penulis', 
              penerbit = '$penerbit', 
              tahun_terbit = '$tahun_terbit' 
              WHERE id_buku = $id_buku";
    
    // Eksekusi query
    $result = mysqli_query($conn, $query);
    
    // Check if query was successful
    if ($result) {
        $affected_rows = mysqli_affected_rows($conn);
        mysqli_close($conn);
        return $affected_rows;
    } else {
        // Kembalikan pesan error jika query gagal
        echo "<!-- Debug: Query error. Query: $query | Error: " . mysqli_error($conn) . " -->";
        mysqli_close($conn);
        return 0;
    }
}

// Fungsi untuk menambahkan data anggota ke database
function add_member($post)
{
    // Koneksi ke database
    global $db;
    
    // Validasi data input
    $nama = $post['nama_anggota'] ?? '';
    $alamat = $post['alamat'] ?? '';
    $email = $post['email'] ?? '';
    $tgl_gabung = isset($post['tgl_gabung']) && is_numeric($post['tgl_gabung']) ? date('Y-m-d', $post['tgl_gabung']) : 'NULL';

    // Query untuk menambahkan data anggota ke database
    $query = "INSERT INTO anggota (nama_anggota, alamat, email, tgl_gabung) VALUES ('$nama', '$alamat', '$email', $tgl_gabung)";
    
    // Eksekusi query
    if (mysqli_query($db, $query)) {
        return mysqli_affected_rows($db);
    } else {
        // Kembalikan pesan error jika query gagal
        throw new mysqli_sql_exception("Error: " . $query . "<br>" . mysqli_error($db));
    }
}

// Fungsi untuk menghapus data anggota dari database
function delete_member($id)
{
    // Koneksi ke database
    global $db;
    
    // Query untuk menghapus data anggota dari database
    $stmt = $db->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}

// Fungsi untuk meminjamkan buku
function pinjamBuku($id_anggota, $id_buku) {
    // Koneksi ke database
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

function edit_member($data) {
    // Koneksi ke database
    $conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');

    // Sanitize and validate input data
    $id_anggota = isset($data['id_anggota']) ? (int)$data['id_anggota'] : 0;
    $nama = mysqli_real_escape_string($conn, $data['nama_anggota']);
    $alamat = mysqli_real_escape_string($conn, $data['alamat']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $tgl_gabung = mysqli_real_escape_string($conn, $data['tgl_gabung']);

    // Query untuk mengedit data member di database
    $query = "UPDATE anggota SET 
                nama_anggota = '$nama', 
                alamat = '$alamat' 
                email = '$email', 
                tgl_gabung = '$tgl_gabung'  
                WHERE id_anggota = $id_anggota";

    // Eksekusi query
    $result = mysqli_query($conn, $query);

    // Check if query was successful
    if ($result) {
        $affected_rows = mysqli_affected_rows($conn);
        mysqli_close($conn);
        return $affected_rows;
    } else {
        // Kembalikan pesan error jika query gagal
        echo "<!-- Debug: Query error. Query: $query | Error: " . mysqli_error($conn) . " -->";
        mysqli_close($conn);
        return 0;
    }
}

function get_member_by_id($id) {
    // Koneksi ke database
    $conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
    
    // Query untuk mengambil data buku berdasarkan ID
    $query = "SELECT * FROM anggota WHERE id_anggota = " . $id;
    
    // Eksekusi query
    $result = mysqli_query($conn, $query);
    
    // Check if query was successful and returned a row
    if ($result && mysqli_num_rows($result) > 0) {
        $buku = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        mysqli_close($conn);
        return $buku;
    } else {
        // Kembalikan pesan error jika query gagal
        echo "<!-- Debug: Query error or no rows returned. Query: $query | Error: " . mysqli_error($conn) . " -->";
        mysqli_close($conn);
        return false;
    }
}

