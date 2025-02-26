<?php
include "koneksi.php";

// Tempat utk menaruh function aplikasi
function add_buku($post)
{
    global $db;
    $id_buku = strip_tags($post['id_buku']);
    $judul = strip_tags($post['judul']);
    $penulis = strip_tags($post['pengarang']);
    $penerbit = strip_tags($post['penerbit']);
    $tahun_terbit = strip_tags($post['tahun_terbit']);
 
    $query = "INSERT INTO buku (id_buku, judul, penulis, penerbit, tahun_terbit) VALUES ('$id_buku', '$judul', '$penulis', '$penerbit', '$tahun_terbit'";
    mysqli_query($db, $query);
    return mysqli_affected_rows($db);
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
    $pengarang = strip_tags($post['pengarang']);
    $penerbit = strip_tags($post['penerbit']);
    $tahun_terbit = strip_tags($post['tahun_terbit']);

    // Query MYSQL edit
    $query = "UPDATE buku SET judul
    = '$judul', pengarang = '$pengarang', penerbit = '$penerbit', tahun_terbit = '$tahun_terbit' WHERE id_buku = $id";
    mysqli_query($db, $query);
    return mysqli_affected_rows($db);
}