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
    $id = strip_tags($post['id_buku']);
    $judul = strip_tags($post['judul']);
    $penulis = strip_tags($post['penulis']);
    $penerbit = strip_tags($post['penerbit']);
    $tahun_terbit = strip_tags($post['tahun_terbit']);

    // Query MYSQL edit
    $stmt = $db->prepare("UPDATE buku SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ? WHERE id_buku = ?");
    $stmt->bind_param("ssssi", $judul, $penulis, $penerbit, $tahun_terbit, $id);
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